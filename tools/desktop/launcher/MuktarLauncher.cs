using System;
using System.Diagnostics;
using System.IO;
using System.Management;
using System.Net;
using System.Net.Sockets;
using System.Security.Cryptography;
using System.Text;
using System.Windows.Forms;
using Microsoft.Win32;

namespace Muktar
{
    internal static class Program
    {
        private const string LicenseSecret = "{{LICENSE_SECRET}}";
        private const string Title = "\u0633\u062c\u0644 \u0627\u0644\u0639\u0648\u0627\u0626\u0644";

        [STAThread]
        private static void Main(string[] args)
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            try
            {
                string root = AppDomain.CurrentDomain.BaseDirectory.TrimEnd(Path.DirectorySeparatorChar);
                if (args.Length > 0 && string.Equals(args[0], "--uninstall", StringComparison.OrdinalIgnoreCase))
                {
                    bool quiet = args.Length > 1 && string.Equals(args[1], "--quiet", StringComparison.OrdinalIgnoreCase);
                    Uninstall(root, quiet);
                    return;
                }

                Run(root);
            }
            catch (Exception ex)
            {
                Alert(ex.Message);
            }
        }

        private static void Run(string root)
        {
            string dataDir = Path.Combine(root, "data");
            string licensePath = Path.Combine(dataDir, "license.key");
            string storagePath = Path.Combine(dataDir, "storage");
            string databasePath = Path.Combine(dataDir, "muktar.sqlite");
            string phpExe = Path.Combine(root, "php", "php.exe");
            string appDir = Path.Combine(root, "app");

            string processorId = ReadProcessorId();
            VerifyLicense(licensePath, processorId);
            EnsureStorage(storagePath);

            int port = FreePort();
            Process php = StartPhp(phpExe, appDir, port, licensePath, storagePath, databasePath);
            try
            {
                WaitForServer(port);
                string profileDir = Path.Combine(dataDir, "edge");
                StartWindow(port, profileDir);
                WaitWhileAppOpen(profileDir, port);
            }
            finally
            {
                StopProcess(php);
            }
        }

        private static void VerifyLicense(string licensePath, string processorId)
        {
            if (!File.Exists(licensePath))
            {
                throw new InvalidOperationException("\u0647\u0630\u0647 \u0627\u0644\u0646\u0633\u062e\u0629 \u063a\u064a\u0631 \u0645\u0641\u0639\u0651\u0644\u0629 \u0639\u0644\u0649 \u0647\u0630\u0647 \u0627\u0644\u062d\u0627\u0633\u0628\u0629.");
            }

            string[] lines = File.ReadAllLines(licensePath, Encoding.UTF8);
            if (lines.Length < 2)
            {
                throw new InvalidOperationException("\u0645\u0644\u0641 \u0627\u0644\u062a\u0631\u062e\u064a\u0635 \u062a\u0627\u0644\u0641.");
            }

            string payload = lines[0].Trim();
            string signature = lines[1].Trim();
            string expected = HmacHex(payload);
            if (!SlowEquals(signature, expected))
            {
                throw new InvalidOperationException("\u0645\u0644\u0641 \u0627\u0644\u062a\u0631\u062e\u064a\u0635 \u063a\u064a\u0631 \u0635\u0627\u0644\u062d.");
            }

            string bound = ExtractProcessorId(payload);
            if (!string.Equals(bound, processorId, StringComparison.OrdinalIgnoreCase))
            {
                throw new InvalidOperationException("\u0644\u0627 \u064a\u0645\u0643\u0646 \u062a\u0634\u063a\u064a\u0644 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0639\u0644\u0649 \u0647\u0630\u0647 \u0627\u0644\u062d\u0627\u0633\u0628\u0629. \u0647\u0630\u0647 \u0627\u0644\u0646\u0633\u062e\u0629 \u0645\u0631\u062a\u0628\u0637\u0629 \u0628\u0645\u0639\u0627\u0644\u062c \u062c\u0647\u0627\u0632 \u0622\u062e\u0631.");
            }
        }

        private static string ReadProcessorId()
        {
            using (var searcher = new ManagementObjectSearcher("SELECT ProcessorId FROM Win32_Processor"))
            {
                foreach (ManagementObject item in searcher.Get())
                {
                    object value = item["ProcessorId"];
                    if (value == null)
                    {
                        continue;
                    }

                    string normalized = NormalizeId(value.ToString());
                    if (normalized.Length >= 8)
                    {
                        return normalized;
                    }
                }
            }

            throw new InvalidOperationException("\u062a\u0639\u0630\u0631 \u0642\u0631\u0627\u0621\u0629 \u0645\u0639\u0631\u0651\u0641 \u0627\u0644\u0645\u0639\u0627\u0644\u062c.");
        }

        private static string NormalizeId(string value)
        {
            var buffer = new StringBuilder();
            foreach (char c in value.ToUpperInvariant())
            {
                if ((c >= 'A' && c <= 'Z') || (c >= '0' && c <= '9'))
                {
                    buffer.Append(c);
                }
            }
            return buffer.ToString();
        }

        private static string ExtractProcessorId(string payload)
        {
            const string key = "\"processor_id\":\"";
            int start = payload.IndexOf(key, StringComparison.Ordinal);
            if (start < 0)
            {
                return string.Empty;
            }

            start += key.Length;
            int end = payload.IndexOf('"', start);
            if (end < 0)
            {
                return string.Empty;
            }

            return NormalizeId(payload.Substring(start, end - start));
        }

        private static string HmacHex(string payload)
        {
            using (var hmac = new HMACSHA256(Encoding.UTF8.GetBytes(LicenseSecret)))
            {
                byte[] hash = hmac.ComputeHash(Encoding.UTF8.GetBytes(payload));
                var hex = new StringBuilder(hash.Length * 2);
                foreach (byte b in hash)
                {
                    hex.Append(b.ToString("x2"));
                }
                return hex.ToString();
            }
        }

        private static bool SlowEquals(string left, string right)
        {
            if (left == null || right == null || left.Length != right.Length)
            {
                return false;
            }

            int diff = 0;
            for (int i = 0; i < left.Length; i++)
            {
                diff |= left[i] ^ right[i];
            }
            return diff == 0;
        }

        private static void EnsureStorage(string storagePath)
        {
            string[] folders =
            {
                Path.Combine(storagePath, "app", "private"),
                Path.Combine(storagePath, "app", "public"),
                Path.Combine(storagePath, "framework", "cache", "data"),
                Path.Combine(storagePath, "framework", "sessions"),
                Path.Combine(storagePath, "framework", "views"),
                Path.Combine(storagePath, "logs")
            };

            foreach (string folder in folders)
            {
                Directory.CreateDirectory(folder);
            }
        }

        private static int FreePort()
        {
            var listener = new TcpListener(IPAddress.Loopback, 0);
            listener.Start();
            int port = ((IPEndPoint)listener.LocalEndpoint).Port;
            listener.Stop();
            return port;
        }

        private static Process StartPhp(
            string phpExe,
            string appDir,
            int port,
            string licensePath,
            string storagePath,
            string databasePath)
        {
            if (!File.Exists(phpExe))
            {
                throw new InvalidOperationException("\u0645\u0644\u0641\u0627\u062a \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u063a\u064a\u0631 \u0645\u0643\u062a\u0645\u0644\u0629.");
            }

            string publicDir = Path.Combine(appDir, "public");
            string router = Path.Combine(appDir, "vendor", "laravel", "framework", "src", "Illuminate", "Foundation", "resources", "server.php");
            if (!Directory.Exists(publicDir) || !File.Exists(router))
            {
                throw new InvalidOperationException("\u0645\u0644\u0641\u0627\u062a \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u063a\u064a\u0631 \u0645\u0643\u062a\u0645\u0644\u0629.");
            }

            string phpDir = Path.GetDirectoryName(phpExe);
            string errorLog = Path.Combine(Path.GetDirectoryName(storagePath) ?? phpDir, "php-server.log");

            var info = new ProcessStartInfo
            {
                FileName = phpExe,
                Arguments = "-S 127.0.0.1:" + port +
                    " -t \"" + publicDir + "\"" +
                    " \"" + router + "\"",
                WorkingDirectory = publicDir,
                UseShellExecute = false,
                CreateNoWindow = true,
                WindowStyle = ProcessWindowStyle.Hidden,
                RedirectStandardError = true,
                RedirectStandardOutput = true
            };

            info.EnvironmentVariables["MUKTAR_DESKTOP"] = "true";
            info.EnvironmentVariables["MUKTAR_LICENSE_PATH"] = licensePath;
            info.EnvironmentVariables["MUKTAR_STORAGE_PATH"] = storagePath;
            info.EnvironmentVariables["DB_CONNECTION"] = "sqlite";
            info.EnvironmentVariables["DB_DATABASE"] = databasePath;
            info.EnvironmentVariables["APP_URL"] = "http://127.0.0.1:" + port;
            info.EnvironmentVariables["APP_ENV"] = "production";
            info.EnvironmentVariables["APP_DEBUG"] = "false";
            info.EnvironmentVariables["PHPRC"] = phpDir;

            Process process = Process.Start(info);
            if (process == null)
            {
                throw new InvalidOperationException("\u062a\u0639\u0630\u0631 \u062a\u0634\u063a\u064a\u0644 \u062e\u062f\u0645\u0629 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c.");
            }

            process.ErrorDataReceived += (sender, e) =>
            {
                if (!string.IsNullOrEmpty(e.Data))
                {
                    try { File.AppendAllText(errorLog, e.Data + Environment.NewLine); } catch { }
                }
            };
            process.BeginErrorReadLine();
            process.BeginOutputReadLine();

            return process;
        }

        private static void WaitForServer(int port)
        {
            var timer = Stopwatch.StartNew();
            Exception last = null;
            while (timer.Elapsed < TimeSpan.FromSeconds(45))
            {
                foreach (string path in new[] { "/up", "/" })
                {
                    try
                    {
                        var request = (HttpWebRequest)WebRequest.Create("http://127.0.0.1:" + port + path);
                        request.Timeout = 2000;
                        request.ReadWriteTimeout = 2000;
                        using (var response = (HttpWebResponse)request.GetResponse())
                        {
                            int code = (int)response.StatusCode;
                            if (code >= 200 && code < 500)
                            {
                                return;
                            }
                        }
                    }
                    catch (WebException ex)
                    {
                        last = ex;
                        HttpWebResponse response = ex.Response as HttpWebResponse;
                        if (response != null)
                        {
                            int code = (int)response.StatusCode;
                            // App is responding even with an application error page.
                            if (code >= 400 && code < 600 && path == "/")
                            {
                                return;
                            }
                        }
                    }
                    catch (Exception ex)
                    {
                        last = ex;
                    }
                }

                System.Threading.Thread.Sleep(300);
            }

            string detail = last == null ? string.Empty : ("\n" + last.Message);
            throw new InvalidOperationException("\u062a\u0639\u0630\u0631 \u062a\u0634\u063a\u064a\u0644 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c. \u062d\u0627\u0648\u0644 \u0645\u0631\u0629 \u0623\u062e\u0631\u0649." + detail);
        }

        private static Process StartWindow(int port, string profileDir)
        {
            Directory.CreateDirectory(profileDir);
            string url = "http://127.0.0.1:" + port;
            string edge = FindEdge();
            string root = AppDomain.CurrentDomain.BaseDirectory.TrimEnd(Path.DirectorySeparatorChar);
            string iconPath = Path.Combine(root, "Muktar.ico");
            if (!File.Exists(iconPath))
            {
                iconPath = Path.Combine(root, "Muktar.exe");
            }

            if (edge != null)
            {
                string arguments = "--app=" + url +
                    " --user-data-dir=\"" + profileDir + "\"" +
                    " --no-first-run --disable-features=TranslateUI --window-size=1400,900";

                string shortcut = Path.Combine(profileDir, "MuktarApp.lnk");
                WriteAppShortcut(shortcut, edge, arguments, root, iconPath);

                Process process = Process.Start(new ProcessStartInfo
                {
                    FileName = shortcut,
                    UseShellExecute = true,
                    WorkingDirectory = root
                });

                if (process == null)
                {
                    // Fallback: start Edge directly
                    process = Process.Start(new ProcessStartInfo
                    {
                        FileName = edge,
                        Arguments = arguments,
                        UseShellExecute = false
                    });
                }

                if (process == null)
                {
                    throw new InvalidOperationException("\u062a\u0639\u0630\u0631 \u0641\u062a\u062d \u0646\u0627\u0641\u0630\u0629 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c.");
                }

                return process;
            }

            Process browser = Process.Start(new ProcessStartInfo
            {
                FileName = url,
                UseShellExecute = true
            });
            if (browser == null)
            {
                throw new InvalidOperationException("\u062a\u0639\u0630\u0631 \u0641\u062a\u062d \u0646\u0627\u0641\u0630\u0629 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c.");
            }

            return browser;
        }

        private static void WriteAppShortcut(
            string linkPath,
            string edgePath,
            string arguments,
            string workDir,
            string iconPath)
        {
            string scriptPath = Path.Combine(Path.GetTempPath(), "muktar-app-lnk-" + Guid.NewGuid().ToString("N") + ".vbs");
            string script =
                "Set sh = CreateObject(\"WScript.Shell\")\r\n" +
                "Set s = sh.CreateShortcut(\"" + EscapeVbs(linkPath) + "\")\r\n" +
                "s.TargetPath = \"" + EscapeVbs(edgePath) + "\"\r\n" +
                "s.Arguments = \"" + EscapeVbs(arguments) + "\"\r\n" +
                "s.WorkingDirectory = \"" + EscapeVbs(workDir) + "\"\r\n" +
                "s.WindowStyle = 1\r\n" +
                "s.IconLocation = \"" + EscapeVbs(iconPath) + "\"\r\n" +
                "s.Description = \"" + EscapeVbs(Title) + "\"\r\n" +
                "s.Save\r\n";

            File.WriteAllText(scriptPath, script, Encoding.Unicode);
            try
            {
                var info = new ProcessStartInfo
                {
                    FileName = "wscript.exe",
                    Arguments = "//nologo \"" + scriptPath + "\"",
                    UseShellExecute = false,
                    CreateNoWindow = true,
                    WindowStyle = ProcessWindowStyle.Hidden
                };
                using (Process process = Process.Start(info))
                {
                    if (process != null)
                    {
                        process.WaitForExit(10000);
                    }
                }
            }
            finally
            {
                try { File.Delete(scriptPath); } catch { }
            }
        }

        private static string EscapeVbs(string value)
        {
            return (value ?? string.Empty).Replace("\"", "\"\"");
        }

        private static void WaitWhileAppOpen(string profileDir, int port)
        {
            string marker = profileDir.Replace("\\", "\\\\");
            DateTime started = DateTime.UtcNow;
            bool sawEdge = false;

            while (true)
            {
                System.Threading.Thread.Sleep(1000);

                bool edgeRunning = false;
                try
                {
                    using (var searcher = new ManagementObjectSearcher(
                        "SELECT ProcessId, CommandLine FROM Win32_Process WHERE Name = 'msedge.exe'"))
                    {
                        foreach (ManagementObject item in searcher.Get())
                        {
                            string command = (item["CommandLine"] ?? string.Empty).ToString();
                            if (command.IndexOf(profileDir, StringComparison.OrdinalIgnoreCase) >= 0 ||
                                command.IndexOf(marker, StringComparison.OrdinalIgnoreCase) >= 0)
                            {
                                edgeRunning = true;
                                sawEdge = true;
                                break;
                            }
                        }
                    }
                }
                catch
                {
                    edgeRunning = IsPortOpen(port);
                }

                if (edgeRunning)
                {
                    continue;
                }

                if (sawEdge)
                {
                    System.Threading.Thread.Sleep(1500);
                    if (!IsPortOpen(port))
                    {
                        return;
                    }

                    // Edge closed; stop the local server.
                    return;
                }

                // Edge never appeared; keep server alive for a short while then stop.
                if ((DateTime.UtcNow - started).TotalSeconds > 20 && !IsPortOpen(port))
                {
                    return;
                }

                if ((DateTime.UtcNow - started).TotalSeconds > 120 && !edgeRunning)
                {
                    return;
                }
            }
        }

        private static bool IsPortOpen(int port)
        {
            try
            {
                using (var client = new TcpClient())
                {
                    var result = client.BeginConnect(IPAddress.Loopback, port, null, null);
                    bool success = result.AsyncWaitHandle.WaitOne(TimeSpan.FromMilliseconds(400));
                    if (!success)
                    {
                        return false;
                    }
                    client.EndConnect(result);
                    return true;
                }
            }
            catch
            {
                return false;
            }
        }

        private static string FindEdge()
        {
            string[] paths =
            {
                Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ProgramFilesX86), "Microsoft", "Edge", "Application", "msedge.exe"),
                Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ProgramFiles), "Microsoft", "Edge", "Application", "msedge.exe")
            };

            foreach (string path in paths)
            {
                if (File.Exists(path))
                {
                    return path;
                }
            }

            return null;
        }

        private static void StopProcess(Process process)
        {
            if (process == null)
            {
                return;
            }

            try
            {
                if (!process.HasExited)
                {
                    process.Kill();
                    process.WaitForExit(3000);
                }
            }
            catch
            {
            }
        }

        private static void Uninstall(string root, bool quiet)
        {
            if (!quiet)
            {
                DialogResult answer = MessageBox.Show(
                    "\u0647\u0644 \u062a\u0631\u064a\u062f \u0625\u0632\u0627\u0644\u0629 \u0633\u062c\u0644 \u0627\u0644\u0639\u0648\u0627\u0626\u0644 \u0645\u0646 \u0647\u0630\u0647 \u0627\u0644\u062d\u0627\u0633\u0628\u0629\u061f",
                    Title,
                    MessageBoxButtons.YesNo,
                    MessageBoxIcon.Question,
                    MessageBoxDefaultButton.Button2,
                    MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);

                if (answer != DialogResult.Yes)
                {
                    return;
                }
            }

            int selfId = Process.GetCurrentProcess().Id;
            foreach (string name in new[] { "Muktar", "php", "msedge" })
            {
                foreach (Process process in Process.GetProcessesByName(name))
                {
                    try
                    {
                        if (process.Id == selfId)
                        {
                            continue;
                        }

                        string path = string.Empty;
                        try { path = process.MainModule.FileName; } catch { path = string.Empty; }

                        bool kill =
                            name == "Muktar" ||
                            (!string.IsNullOrEmpty(path) && path.StartsWith(root, StringComparison.OrdinalIgnoreCase));

                        if (name == "msedge")
                        {
                            kill = false;
                            try
                            {
                                using (var searcher = new ManagementObjectSearcher(
                                    "SELECT ProcessId, CommandLine FROM Win32_Process WHERE ProcessId = " + process.Id))
                                {
                                    foreach (ManagementObject item in searcher.Get())
                                    {
                                        string command = (item["CommandLine"] ?? string.Empty).ToString();
                                        if (command.IndexOf(Path.Combine(root, "data", "edge"), StringComparison.OrdinalIgnoreCase) >= 0)
                                        {
                                            kill = true;
                                        }
                                    }
                                }
                            }
                            catch
                            {
                            }
                        }

                        if (kill)
                        {
                            process.Kill();
                            process.WaitForExit(3000);
                        }
                    }
                    catch
                    {
                    }
                }
            }

            string desktop = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
            foreach (string file in new[]
            {
                Path.Combine(desktop, "Muktar.lnk"),
                Path.Combine(desktop, Title + ".lnk"),
                Path.Combine(desktop, "Muktar.url"),
                Path.Combine(desktop, Title + ".url")
            })
            {
                try { if (File.Exists(file)) File.Delete(file); } catch { }
            }

            try
            {
                Registry.CurrentUser.DeleteSubKeyTree(
                    @"Software\Microsoft\Windows\CurrentVersion\Uninstall\Muktar",
                    false);
            }
            catch
            {
            }

            string bat = Path.Combine(
                Path.GetTempPath(),
                "muktar-uninstall-" + Process.GetCurrentProcess().Id + ".cmd");
            string batBody =
                "@echo off\r\n" +
                "ping 127.0.0.1 -n 3 >nul\r\n" +
                "set /a n=0\r\n" +
                ":retry\r\n" +
                "set /a n+=1\r\n" +
                "attrib -h -s -r \"" + root + "\" /S /D >nul 2>&1\r\n" +
                "rd /s /q \"" + root + "\" >nul 2>&1\r\n" +
                "if exist \"" + root + "\" if %n% LSS 15 (\r\n" +
                "  ping 127.0.0.1 -n 2 >nul\r\n" +
                "  goto retry\r\n" +
                ")\r\n" +
                "del \"%~f0\" >nul 2>&1\r\n";
            File.WriteAllText(bat, batBody, Encoding.ASCII);

            Process.Start(new ProcessStartInfo
            {
                FileName = "cmd.exe",
                Arguments = "/c \"" + bat + "\"",
                WindowStyle = ProcessWindowStyle.Hidden,
                CreateNoWindow = true,
                UseShellExecute = false
            });
        }

        private static void Alert(string message)
        {
            MessageBox.Show(
                message,
                Title,
                MessageBoxButtons.OK,
                MessageBoxIcon.Error,
                MessageBoxDefaultButton.Button1,
                MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
        }
    }
}
