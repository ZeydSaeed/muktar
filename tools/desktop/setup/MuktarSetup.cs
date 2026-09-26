using System;
using System.ComponentModel;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.IO.Compression;
using System.Management;
using System.Security.Cryptography;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using Microsoft.Win32;

namespace Muktar
{
    internal static class Setup
    {
        private const string Marker = "MUKTAR_PAYLOAD_V1";
        // Fixed-size slot patched by MuktarBinder.exe (underscores = unbound template).
        private const string BoundProcessorIdSlot =
            "MUKTAR_PID<<________________________________________________________________>>MUKTAR_PID";
        private const string LicenseSecret = "{{LICENSE_SECRET}}";
        private const string AppKey = "{{APP_KEY}}";
        private const string Title = "\u0633\u062c\u0644 \u0627\u0644\u0639\u0648\u0627\u0626\u0644";
        private const string InstallFolderName = "Muktar";

        [STAThread]
        private static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            try
            {
                string processorId = ReadProcessorId();
                string boundId = GetConfiguredBoundProcessorId();
                if (!string.IsNullOrEmpty(boundId) &&
                    !string.Equals(boundId, processorId, StringComparison.OrdinalIgnoreCase))
                {
                    throw new InvalidOperationException(
                        "\u0644\u0627 \u064a\u0645\u0643\u0646 \u062a\u0646\u0635\u064a\u0628 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0639\u0644\u0649 \u0647\u0630\u0647 \u0627\u0644\u062d\u0627\u0633\u0628\u0629. \u0647\u0630\u0647 \u0627\u0644\u0646\u0633\u062e\u0629 \u0645\u062e\u0635\u0635\u0629 \u0644\u062c\u0647\u0627\u0632 \u0622\u062e\u0631.");
                }

                Application.Run(new SetupWizard(processorId));
            }
            catch (Exception ex)
            {
                Exception current = ex;
                while (current.InnerException != null)
                {
                    current = current.InnerException;
                }

                MessageBox.Show(
                    current.Message,
                    Title,
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error,
                    MessageBoxDefaultButton.Button1,
                    MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
            }
        }

        private sealed class SetupWizard : Form
        {
            private readonly string processorId;
            private readonly string root;
            private readonly string launcher;

            private readonly Panel header;
            private readonly Label titleLabel;
            private readonly Label subtitleLabel;
            private readonly Panel content;
            private readonly Panel footer;
            private readonly Button backButton;
            private readonly Button nextButton;
            private readonly Button cancelButton;
            private readonly ProgressBar progressBar;
            private readonly Label statusLabel;
            private readonly CheckBox desktopShortcutCheck;
            private readonly CheckBox launchAfterCheck;

            private int step;
            private bool installSucceeded;
            private bool createDesktopShortcut = true;
            private bool launchAfterInstall = true;
            private BackgroundWorker worker;

            public SetupWizard(string processorId)
            {
                this.processorId = processorId;
                root = Path.Combine(
                    Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
                    InstallFolderName);
                launcher = Path.Combine(root, "Muktar.exe");

                Text = Title + " - \u0627\u0644\u062a\u0646\u0635\u064a\u0628";
                RightToLeft = RightToLeft.Yes;
                RightToLeftLayout = true;
                Font = new Font("Segoe UI", 10F, FontStyle.Regular, GraphicsUnit.Point);
                FormBorderStyle = FormBorderStyle.FixedDialog;
                MaximizeBox = false;
                MinimizeBox = false;
                StartPosition = FormStartPosition.CenterScreen;
                ClientSize = new Size(560, 420);
                BackColor = Color.White;
                ShowInTaskbar = true;
                ShowIcon = true;
                ApplyFormIcon(this);

                header = new Panel
                {
                    Dock = DockStyle.Top,
                    Height = 78,
                    BackColor = Color.FromArgb(15, 76, 129),
                    Padding = new Padding(18, 14, 18, 14)
                };

                titleLabel = new Label
                {
                    AutoSize = false,
                    Dock = DockStyle.Top,
                    Height = 28,
                    ForeColor = Color.White,
                    Font = new Font("Segoe UI", 14F, FontStyle.Bold, GraphicsUnit.Point),
                    TextAlign = ContentAlignment.MiddleRight
                };

                subtitleLabel = new Label
                {
                    AutoSize = false,
                    Dock = DockStyle.Top,
                    Height = 24,
                    ForeColor = Color.FromArgb(210, 230, 245),
                    TextAlign = ContentAlignment.MiddleRight
                };

                header.Controls.Add(subtitleLabel);
                header.Controls.Add(titleLabel);

                content = new Panel
                {
                    Dock = DockStyle.Fill,
                    Padding = new Padding(24, 18, 24, 12)
                };

                footer = new Panel
                {
                    Dock = DockStyle.Bottom,
                    Height = 58,
                    BackColor = Color.FromArgb(245, 247, 250),
                    Padding = new Padding(12, 10, 12, 10)
                };

                cancelButton = MakeButton("\u0625\u0644\u063a\u0627\u0621", 100);
                cancelButton.Anchor = AnchorStyles.Left | AnchorStyles.Top;
                cancelButton.Location = new Point(12, 10);
                cancelButton.Click += (s, e) => CloseWizard();

                nextButton = MakeButton("\u0627\u0644\u062a\u0627\u0644\u064a", 110);
                nextButton.Anchor = AnchorStyles.Right | AnchorStyles.Top;
                nextButton.Location = new Point(ClientSize.Width - 134, 10);
                nextButton.Click += (s, e) => OnNext();

                backButton = MakeButton("\u0627\u0644\u0633\u0627\u0628\u0642", 110);
                backButton.Anchor = AnchorStyles.Right | AnchorStyles.Top;
                backButton.Location = new Point(ClientSize.Width - 250, 10);
                backButton.Click += (s, e) => OnBack();

                footer.Controls.Add(cancelButton);
                footer.Controls.Add(backButton);
                footer.Controls.Add(nextButton);

                progressBar = new ProgressBar
                {
                    Style = ProgressBarStyle.Marquee,
                    MarqueeAnimationSpeed = 30,
                    Height = 22,
                    Dock = DockStyle.Top,
                    Visible = false
                };

                statusLabel = new Label
                {
                    AutoSize = false,
                    Dock = DockStyle.Top,
                    Height = 60,
                    TextAlign = ContentAlignment.TopRight,
                    Visible = false
                };

                desktopShortcutCheck = new CheckBox
                {
                    AutoSize = true,
                    Checked = true,
                    Text = "\u0625\u0646\u0634\u0627\u0621 \u0627\u062e\u062a\u0635\u0627\u0631 \u0639\u0644\u0649 \u0633\u0637\u062d \u0627\u0644\u0645\u0643\u062a\u0628"
                };
                desktopShortcutCheck.CheckedChanged += (s, e) => createDesktopShortcut = desktopShortcutCheck.Checked;

                launchAfterCheck = new CheckBox
                {
                    AutoSize = true,
                    Checked = true,
                    Text = "\u062a\u0634\u063a\u064a\u0644 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0628\u0639\u062f \u0627\u0644\u062a\u0646\u0635\u064a\u0628"
                };
                launchAfterCheck.CheckedChanged += (s, e) => launchAfterInstall = launchAfterCheck.Checked;

                Controls.Add(content);
                Controls.Add(footer);
                Controls.Add(header);

                Resize += (s, e) => LayoutButtons();
                FormClosing += OnFormClosing;

                bool alreadyInstalled =
                    File.Exists(launcher) &&
                    File.Exists(Path.Combine(root, "data", "license.key"));

                step = alreadyInstalled ? 10 : 0;
                ShowStep();
            }

            private Button MakeButton(string text, int width)
            {
                return new Button
                {
                    Text = text,
                    Width = width,
                    Height = 34,
                    FlatStyle = FlatStyle.System,
                    UseVisualStyleBackColor = true
                };
            }

            private void LayoutButtons()
            {
                nextButton.Location = new Point(footer.ClientSize.Width - nextButton.Width - 12, 10);
                backButton.Location = new Point(nextButton.Left - backButton.Width - 8, 10);
                cancelButton.Location = new Point(12, 10);
            }

            private void ShowStep()
            {
                content.Controls.Clear();
                progressBar.Visible = false;
                statusLabel.Visible = false;
                backButton.Enabled = true;
                nextButton.Enabled = true;
                cancelButton.Enabled = true;
                cancelButton.Text = "\u0625\u0644\u063a\u0627\u0621";

                if (step == 0)
                {
                    titleLabel.Text = "\u0645\u0631\u062d\u0628\u064b\u0627";
                    subtitleLabel.Text = "\u0645\u0639\u0627\u0644\u062c \u062a\u0646\u0635\u064a\u0628 " + Title;
                    backButton.Enabled = false;
                    nextButton.Text = "\u0627\u0644\u062a\u0627\u0644\u064a";

                    content.Controls.Add(MakeBody(
                        "\u0633\u064a\u0642\u0648\u0645 \u0647\u0630\u0627 \u0627\u0644\u0645\u0639\u0627\u0644\u062c \u0628\u062a\u0646\u0635\u064a\u0628 \u0628\u0631\u0646\u0627\u0645\u062c " + Title + " \u0639\u0644\u0649 \u062c\u0647\u0627\u0632\u0643.\r\n\r\n" +
                        "\u2022 \u0644\u0627 \u064a\u062d\u062a\u0627\u062c \u0625\u0644\u0649 \u0627\u062a\u0635\u0627\u0644 \u0628\u0627\u0644\u0625\u0646\u062a\u0631\u0646\u062a\r\n" +
                        "\u2022 \u0633\u064a\u062a\u0645 \u0625\u0646\u0634\u0627\u0621 \u0627\u062e\u062a\u0635\u0627\u0631 \u0639\u0644\u0649 \u0633\u0637\u062d \u0627\u0644\u0645\u0643\u062a\u0628\r\n" +
                        "\u2022 \u064a\u0645\u0643\u0646 \u0625\u0632\u0627\u0644\u0629 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0644\u0627\u062d\u0642\u064b\u0627 \u0645\u0646 \u0644\u0648\u062d\u0629 \u0627\u0644\u062a\u062d\u0643\u0645\r\n\r\n" +
                        "\u0627\u0636\u063a\u0637 \u00ab\u0627\u0644\u062a\u0627\u0644\u064a\u00bb \u0644\u0644\u0645\u062a\u0627\u0628\u0639\u0629."));
                }
                else if (step == 1)
                {
                    titleLabel.Text = "\u062c\u0627\u0647\u0632 \u0644\u0644\u062a\u0646\u0635\u064a\u0628";
                    subtitleLabel.Text = "\u0631\u0627\u062c\u0639 \u062e\u064a\u0627\u0631\u0627\u062a \u0627\u0644\u062a\u0646\u0635\u064a\u0628";
                    nextButton.Text = "\u062a\u0646\u0635\u064a\u0628";

                    var body = MakeBody(
                        "\u0633\u064a\u062a\u0645 \u062a\u0646\u0635\u064a\u0628 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0641\u064a:\r\n" + root + "\r\n\r\n" +
                        "\u0627\u062e\u062a\u0631 \u0627\u0644\u062e\u064a\u0627\u0631\u0627\u062a \u0627\u0644\u062a\u0627\u0644\u064a\u0629 \u062b\u0645 \u0627\u0636\u063a\u0637 \u00ab\u062a\u0646\u0635\u064a\u0628\u00bb.");

                    desktopShortcutCheck.Checked = createDesktopShortcut;
                    launchAfterCheck.Checked = launchAfterInstall;
                    desktopShortcutCheck.Location = new Point(24, 150);
                    launchAfterCheck.Location = new Point(24, 185);

                    content.Controls.Add(launchAfterCheck);
                    content.Controls.Add(desktopShortcutCheck);
                    content.Controls.Add(body);
                }
                else if (step == 2)
                {
                    titleLabel.Text = "\u062c\u0627\u0631\u064d \u0627\u0644\u062a\u0646\u0635\u064a\u0628";
                    subtitleLabel.Text = "\u064a\u0631\u062c\u0649 \u0627\u0644\u0627\u0646\u062a\u0638\u0627\u0631...";
                    backButton.Enabled = false;
                    nextButton.Enabled = false;
                    cancelButton.Enabled = false;
                    nextButton.Text = "\u0627\u0644\u062a\u0627\u0644\u064a";

                    statusLabel.Visible = true;
                    statusLabel.Text = "\u062c\u0627\u0631\u064d \u062a\u062d\u0636\u064a\u0631 \u0627\u0644\u0645\u0644\u0641\u0627\u062a...";
                    progressBar.Visible = true;
                    progressBar.Dock = DockStyle.None;
                    progressBar.Width = content.ClientSize.Width - 48;
                    progressBar.Location = new Point(24, 90);
                    statusLabel.Dock = DockStyle.None;
                    statusLabel.Width = content.ClientSize.Width - 48;
                    statusLabel.Location = new Point(24, 30);
                    statusLabel.Height = 50;

                    content.Controls.Add(progressBar);
                    content.Controls.Add(statusLabel);

                    BeginInstall();
                }
                else if (step == 3)
                {
                    titleLabel.Text = "\u0627\u0643\u062a\u0645\u0644 \u0627\u0644\u062a\u0646\u0635\u064a\u0628";
                    subtitleLabel.Text = installSucceeded
                        ? "\u062a\u0645 \u062a\u0646\u0635\u064a\u0628 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0628\u0646\u062c\u0627\u062d"
                        : "\u0641\u0634\u0644 \u0627\u0644\u062a\u0646\u0635\u064a\u0628";
                    backButton.Enabled = false;
                    cancelButton.Visible = false;
                    nextButton.Text = "\u0625\u0646\u0647\u0627\u0621";
                    nextButton.Enabled = true;

                    string message = installSucceeded
                        ? "\u062a\u0645 \u062a\u0646\u0635\u064a\u0628 " + Title + " \u0628\u0646\u062c\u0627\u062d.\r\n\r\n" +
                          (createDesktopShortcut
                              ? "\u062a\u0645 \u0625\u0646\u0634\u0627\u0621 \u0627\u062e\u062a\u0635\u0627\u0631 \u0639\u0644\u0649 \u0633\u0637\u062d \u0627\u0644\u0645\u0643\u062a\u0628.\r\n\r\n"
                              : string.Empty) +
                          "\u0627\u0636\u063a\u0637 \u00ab\u0625\u0646\u0647\u0627\u0621\u00bb \u0644\u0625\u063a\u0644\u0627\u0642 \u0627\u0644\u0645\u0639\u0627\u0644\u062c."
                        : statusLabel.Text;

                    content.Controls.Add(MakeBody(message));
                }
                else if (step == 10)
                {
                    titleLabel.Text = "\u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0645\u062b\u0628\u0651\u062a";
                    subtitleLabel.Text = Title;
                    backButton.Enabled = false;
                    nextButton.Text = "\u062a\u0634\u063a\u064a\u0644";
                    cancelButton.Text = "\u0625\u063a\u0644\u0627\u0642";

                    desktopShortcutCheck.Checked = true;
                    desktopShortcutCheck.Location = new Point(24, 140);
                    content.Controls.Add(desktopShortcutCheck);
                    content.Controls.Add(MakeBody(
                        "\u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0645\u062b\u0628\u0651\u062a \u0645\u0633\u0628\u0642\u064b\u0627 \u0639\u0644\u0649 \u0647\u0630\u0627 \u0627\u0644\u062c\u0647\u0627\u0632.\r\n\r\n" +
                        "\u064a\u0645\u0643\u0646\u0643 \u0625\u0639\u0627\u062f\u0629 \u0625\u0646\u0634\u0627\u0621 \u0627\u062e\u062a\u0635\u0627\u0631 \u0633\u0637\u062d \u0627\u0644\u0645\u0643\u062a\u0628 \u0623\u0648 \u062a\u0634\u063a\u064a\u0644 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u0627\u0644\u0622\u0646."));
                }

                LayoutButtons();
            }

            private Label MakeBody(string text)
            {
                return new Label
                {
                    AutoSize = false,
                    Dock = DockStyle.Fill,
                    Text = text,
                    TextAlign = ContentAlignment.TopRight
                };
            }

            private void OnBack()
            {
                if (step == 1)
                {
                    step = 0;
                    ShowStep();
                }
            }

            private void OnNext()
            {
                if (step == 0)
                {
                    step = 1;
                    ShowStep();
                    return;
                }

                if (step == 1)
                {
                    step = 2;
                    ShowStep();
                    return;
                }

                if (step == 3)
                {
                    FinishAndClose(launchAfterInstall && installSucceeded);
                    return;
                }

                if (step == 10)
                {
                    try
                    {
                        if (desktopShortcutCheck.Checked)
                        {
                            CreateShortcut(launcher, Path.Combine(root, "Muktar.ico"));
                        }

                        RegisterUninstall(root, launcher, Path.Combine(root, "Muktar.ico"));
                        FinishAndClose(true);
                    }
                    catch (Exception ex)
                    {
                        MessageBox.Show(
                            RootMessage(ex),
                            Title,
                            MessageBoxButtons.OK,
                            MessageBoxIcon.Error,
                            MessageBoxDefaultButton.Button1,
                            MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
                    }
                }
            }

            private void BeginInstall()
            {
                worker = new BackgroundWorker();
                worker.WorkerReportsProgress = true;
                worker.DoWork += (s, e) =>
                {
                    Report("\u062c\u0627\u0631\u064d \u0625\u064a\u0642\u0627\u0641 \u0627\u0644\u0639\u0645\u0644\u064a\u0627\u062a \u0627\u0644\u0633\u0627\u0628\u0642\u0629...");
                    StopLeftoverApp();
                    WipeDirectory(root);
                    Directory.CreateDirectory(root);

                    Report("\u062c\u0627\u0631\u064d \u0627\u0633\u062a\u062e\u0631\u0627\u062c \u0645\u0644\u0641\u0627\u062a \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c...");
                    ExtractPayload(ReadPayload(), root);

                    string dataDir = Path.Combine(root, "data");
                    string storagePath = Path.Combine(dataDir, "storage");
                    string licensePath = Path.Combine(dataDir, "license.key");
                    string databasePath = Path.Combine(dataDir, "muktar.sqlite");
                    Directory.CreateDirectory(storagePath);

                    string seedDatabase = Path.Combine(root, "seed.sqlite");
                    if (File.Exists(seedDatabase))
                    {
                        File.Copy(seedDatabase, databasePath, true);
                    }
                    else if (!File.Exists(databasePath))
                    {
                        File.WriteAllBytes(databasePath, new byte[0]);
                    }

                    string phpExe = Path.Combine(root, "php", "php.exe");
                    string appDir = Path.Combine(root, "app");
                    if (!File.Exists(phpExe) || !File.Exists(Path.Combine(appDir, "artisan")))
                    {
                        throw new InvalidOperationException("\u0645\u0644\u0641\u0627\u062a \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c \u063a\u064a\u0631 \u0645\u0643\u062a\u0645\u0644\u0629.");
                    }

                    Report("\u062c\u0627\u0631\u064d \u0625\u0639\u062f\u0627\u062f \u0627\u0644\u0625\u0639\u062f\u0627\u062f\u0627\u062a...");
                    WriteEnv(appDir, databasePath, licensePath, storagePath);
                    WriteLicense(licensePath, processorId);
                    EnsureStorage(storagePath);

                    Report("\u062c\u0627\u0631\u064d \u062a\u0633\u062c\u064a\u0644 \u0627\u0644\u0628\u0631\u0646\u0627\u0645\u062c...");
                    RegisterUninstall(root, launcher, Path.Combine(root, "Muktar.ico"));

                    if (createDesktopShortcut)
                    {
                        Report("\u062c\u0627\u0631\u064d \u0625\u0646\u0634\u0627\u0621 \u0627\u062e\u062a\u0635\u0627\u0631 \u0633\u0637\u062d \u0627\u0644\u0645\u0643\u062a\u0628...");
                        CreateShortcut(launcher, Path.Combine(root, "Muktar.ico"));
                    }

                    HidePath(dataDir);
                    HidePath(Path.Combine(root, "php"));
                    HidePath(appDir);

                    Report("\u0627\u0643\u062a\u0645\u0644.");
                };
                worker.ProgressChanged += (s, e) =>
                {
                    statusLabel.Text = (e.UserState as string) ?? statusLabel.Text;
                };
                worker.RunWorkerCompleted += (s, e) =>
                {
                    if (e.Error != null)
                    {
                        installSucceeded = false;
                        statusLabel.Text = RootMessage(e.Error);
                        step = 3;
                        ShowStep();
                        return;
                    }

                    installSucceeded = true;
                    step = 3;
                    ShowStep();
                };
                worker.RunWorkerAsync();
            }

            private void Report(string message)
            {
                if (worker != null)
                {
                    worker.ReportProgress(0, message);
                }
                Thread.Sleep(120);
            }

            private void FinishAndClose(bool launch)
            {
                if (launch && File.Exists(launcher))
                {
                    try
                    {
                        Process.Start(new ProcessStartInfo
                        {
                            FileName = launcher,
                            WorkingDirectory = root,
                            UseShellExecute = true
                        });
                    }
                    catch
                    {
                    }
                }

                SelfDelete();
                Close();
            }

            private void CloseWizard()
            {
                if (step == 2)
                {
                    return;
                }

                Close();
            }

            private void OnFormClosing(object sender, FormClosingEventArgs e)
            {
                if (step == 2 && worker != null && worker.IsBusy)
                {
                    e.Cancel = true;
                }
            }

            private static string RootMessage(Exception ex)
            {
                Exception current = ex;
                while (current.InnerException != null)
                {
                    current = current.InnerException;
                }
                return current.Message;
            }
        }

        private static void RegisterUninstall(string root, string launcher, string iconPath)
        {
            using (RegistryKey key = Registry.CurrentUser.CreateSubKey(
                @"Software\Microsoft\Windows\CurrentVersion\Uninstall\Muktar"))
            {
                if (key == null)
                {
                    return;
                }

                key.SetValue("DisplayName", Title);
                key.SetValue("DisplayIcon", File.Exists(iconPath) ? iconPath : launcher);
                key.SetValue("Publisher", "Muktar");
                key.SetValue("InstallLocation", root);
                key.SetValue("UninstallString", "\"" + launcher + "\" --uninstall");
                key.SetValue("QuietUninstallString", "\"" + launcher + "\" --uninstall --quiet");
                key.SetValue("NoModify", 1, RegistryValueKind.DWord);
                key.SetValue("NoRepair", 1, RegistryValueKind.DWord);
                key.SetValue("EstimatedSize", EstimateSizeKb(root), RegistryValueKind.DWord);
                key.SetValue("DisplayVersion", "1.0.0");
            }
        }

        private static int EstimateSizeKb(string root)
        {
            long bytes = 0;
            try
            {
                foreach (string file in Directory.GetFiles(root, "*", SearchOption.AllDirectories))
                {
                    try { bytes += new FileInfo(file).Length; } catch { }
                }
            }
            catch
            {
            }

            return (int)Math.Max(1, bytes / 1024);
        }

        private static byte[] ReadPayload()
        {
            string exe = Application.ExecutablePath;
            byte[] all = File.ReadAllBytes(exe);
            byte[] marker = Encoding.ASCII.GetBytes(Marker);
            int pos = FindMarker(all, marker);
            if (pos < 0)
            {
                throw new InvalidOperationException("\u0645\u0644\u0641 \u0627\u0644\u062a\u0646\u0635\u064a\u0628 \u063a\u064a\u0631 \u0645\u0643\u062a\u0645\u0644.");
            }

            int lengthPos = pos + marker.Length;
            long length = BitConverter.ToInt64(all, lengthPos);
            var zip = new byte[length];
            Buffer.BlockCopy(all, lengthPos + 8, zip, 0, (int)length);
            return zip;
        }

        private static int FindMarker(byte[] haystack, byte[] needle)
        {
            for (int i = haystack.Length - needle.Length; i >= 0; i--)
            {
                bool match = true;
                for (int j = 0; j < needle.Length; j++)
                {
                    if (haystack[i + j] != needle[j])
                    {
                        match = false;
                        break;
                    }
                }
                if (match)
                {
                    return i;
                }
            }
            return -1;
        }

        private static string EnvQuote(string value)
        {
            return "\"" + value.Replace("\\", "/").Replace("\"", "") + "\"";
        }

        private static void WriteEnv(string appDir, string databasePath, string licensePath, string storagePath)
        {
            string env = string.Join("\n", new string[]
            {
                "APP_NAME=Muktar",
                "APP_ENV=production",
                "APP_KEY=" + AppKey,
                "APP_DEBUG=false",
                "APP_URL=http://127.0.0.1",
                "APP_LOCALE=ar",
                "APP_FALLBACK_LOCALE=ar",
                "APP_MAINTENANCE_DRIVER=file",
                "LOG_CHANNEL=single",
                "LOG_LEVEL=error",
                "DB_CONNECTION=sqlite",
                "DB_DATABASE=" + EnvQuote(databasePath),
                "DB_FOREIGN_KEYS=true",
                "SESSION_DRIVER=database",
                "SESSION_LIFETIME=120",
                "BROADCAST_CONNECTION=log",
                "FILESYSTEM_DISK=local",
                "QUEUE_CONNECTION=database",
                "CACHE_STORE=database",
                "MAIL_MAILER=log",
                "MUKTAR_DESKTOP=true",
                "MUKTAR_LICENSE_PATH=" + EnvQuote(licensePath),
                "MUKTAR_STORAGE_PATH=" + EnvQuote(storagePath)
            });

            File.WriteAllText(Path.Combine(appDir, ".env"), env + "\n", new UTF8Encoding(false));
        }

        private static void WriteLicense(string path, string processorId)
        {
            string payload = "{\"processor_id\":\"" + processorId + "\",\"bound_at\":\"" + DateTime.UtcNow.ToString("yyyy-MM-ddTHH:mm:ss+00:00") + "\"}";
            Directory.CreateDirectory(Path.GetDirectoryName(path));
            File.WriteAllText(path, payload + "\n" + HmacHex(payload), new UTF8Encoding(false));
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

        private static void ExtractPayload(byte[] zipBytes, string destination)
        {
            string tempZip = Path.Combine(Path.GetTempPath(), "muktar-" + Guid.NewGuid().ToString("N") + ".zip");
            File.WriteAllBytes(tempZip, zipBytes);

            try
            {
                using (ZipArchive archive = ZipFile.OpenRead(tempZip))
                {
                    string destinationFull = Path.GetFullPath(destination).TrimEnd(Path.DirectorySeparatorChar)
                        + Path.DirectorySeparatorChar;

                    foreach (ZipArchiveEntry entry in archive.Entries)
                    {
                        string relative = (entry.FullName ?? string.Empty)
                            .Replace('/', Path.DirectorySeparatorChar)
                            .Replace('\\', Path.DirectorySeparatorChar);

                        if (string.IsNullOrWhiteSpace(relative) ||
                            relative.EndsWith(Path.DirectorySeparatorChar.ToString()) ||
                            string.IsNullOrEmpty(entry.Name))
                        {
                            continue;
                        }

                        string full = Path.GetFullPath(Path.Combine(destination, relative));
                        if (!full.StartsWith(destinationFull, StringComparison.OrdinalIgnoreCase))
                        {
                            continue;
                        }

                        string parent = Path.GetDirectoryName(full);
                        if (!string.IsNullOrEmpty(parent))
                        {
                            Directory.CreateDirectory(parent);
                        }

                        entry.ExtractToFile(full, true);
                    }
                }
            }
            finally
            {
                try { File.Delete(tempZip); } catch { }
            }
        }

        private static void StopLeftoverApp()
        {
            foreach (Process process in Process.GetProcessesByName("Muktar"))
            {
                try
                {
                    process.Kill();
                    process.WaitForExit(3000);
                }
                catch
                {
                }
            }
        }

        private static void WipeDirectory(string path)
        {
            if (string.IsNullOrWhiteSpace(path) || !Directory.Exists(path))
            {
                return;
            }

            try
            {
                var directory = new DirectoryInfo(path);
                directory.Attributes = FileAttributes.Normal;
                foreach (FileSystemInfo item in directory.GetFileSystemInfos("*", SearchOption.AllDirectories))
                {
                    item.Attributes = FileAttributes.Normal;
                }
            }
            catch
            {
            }

            for (int attempt = 0; attempt < 4; attempt++)
            {
                try
                {
                    var info = new ProcessStartInfo
                    {
                        FileName = "cmd.exe",
                        Arguments = "/c rd /s /q \"" + path + "\"",
                        UseShellExecute = false,
                        CreateNoWindow = true,
                        WindowStyle = ProcessWindowStyle.Hidden
                    };
                    using (Process process = Process.Start(info))
                    {
                        process.WaitForExit(15000);
                    }
                }
                catch
                {
                }

                if (!Directory.Exists(path))
                {
                    return;
                }

                Thread.Sleep(400);
            }

            if (Directory.Exists(path))
            {
                string backup = path + ".old." + DateTime.Now.ToString("yyyyMMddHHmmss");
                try
                {
                    Directory.Move(path, backup);
                }
                catch
                {
                }
            }
        }

        private static void CreateShortcut(string launcher, string iconPath)
        {
            try
            {
                string desktop = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
                if (string.IsNullOrWhiteSpace(desktop) || !Directory.Exists(desktop))
                {
                    desktop = Environment.GetFolderPath(Environment.SpecialFolder.Desktop);
                }

                string linkPath = Path.Combine(desktop, "Muktar.lnk");
                string workDir = Path.GetDirectoryName(launcher) ?? string.Empty;
                string icon = File.Exists(iconPath) ? iconPath : launcher;
                string scriptPath = Path.Combine(Path.GetTempPath(), "muktar-shortcut-" + Guid.NewGuid().ToString("N") + ".vbs");

                string script =
                    "Set sh = CreateObject(\"WScript.Shell\")\r\n" +
                    "Set s = sh.CreateShortcut(\"" + EscapeVbs(linkPath) + "\")\r\n" +
                    "s.TargetPath = \"" + EscapeVbs(launcher) + "\"\r\n" +
                    "s.WorkingDirectory = \"" + EscapeVbs(workDir) + "\"\r\n" +
                    "s.IconLocation = \"" + EscapeVbs(icon) + "\"\r\n" +
                    "s.Description = \"" + EscapeVbs(Title) + "\"\r\n" +
                    "s.Save\r\n";

                File.WriteAllText(scriptPath, script, Encoding.Unicode);

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
                        process.WaitForExit(15000);
                    }
                }

                try { File.Delete(scriptPath); } catch { }

                if (!File.Exists(linkPath))
                {
                    WriteUrlShortcut(desktop, launcher, icon);
                }
            }
            catch
            {
                try
                {
                    string desktop = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
                    WriteUrlShortcut(desktop, launcher, iconPath);
                }
                catch
                {
                }
            }
        }

        private static string EscapeVbs(string value)
        {
            return (value ?? string.Empty).Replace("\"", "\"\"");
        }

        private static void WriteUrlShortcut(string desktop, string launcher, string iconPath)
        {
            if (string.IsNullOrWhiteSpace(desktop) || !Directory.Exists(desktop))
            {
                return;
            }

            string path = Path.Combine(desktop, "Muktar.url");
            string icon = File.Exists(iconPath) ? iconPath : launcher;
            string content =
                "[InternetShortcut]\r\n" +
                "URL=file:///" + launcher.Replace("\\", "/") + "\r\n" +
                "IconFile=" + icon + "\r\n" +
                "IconIndex=0\r\n";
            File.WriteAllText(path, content, new UTF8Encoding(false));
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

        private static void HidePath(string path)
        {
            if (Directory.Exists(path))
            {
                new DirectoryInfo(path).Attributes |= FileAttributes.Hidden;
            }
            else if (File.Exists(path))
            {
                File.SetAttributes(path, File.GetAttributes(path) | FileAttributes.Hidden);
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
            foreach (char c in (value ?? string.Empty).ToUpperInvariant())
            {
                if ((c >= 'A' && c <= 'Z') || (c >= '0' && c <= '9'))
                {
                    buffer.Append(c);
                }
            }
            return buffer.ToString();
        }

        private static string GetConfiguredBoundProcessorId()
        {
            const string open = "MUKTAR_PID<<";
            const string close = ">>MUKTAR_PID";
            string slot = BoundProcessorIdSlot;
            int start = slot.IndexOf(open, StringComparison.Ordinal);
            int end = slot.LastIndexOf(close, StringComparison.Ordinal);
            if (start < 0 || end < 0 || end <= start)
            {
                return string.Empty;
            }

            string inner = slot.Substring(start + open.Length, end - (start + open.Length));
            return NormalizeId(inner);
        }

        private static void SelfDelete()
        {
            string exe = Path.GetFullPath(Application.ExecutablePath);
            // Keep the master installer on the seller PC permanently.
            if (exe.IndexOf(@"\tools\desktop\dist\", StringComparison.OrdinalIgnoreCase) >= 0)
            {
                return;
            }

            Process.Start(new ProcessStartInfo
            {
                FileName = "cmd.exe",
                Arguments = "/c ping 127.0.0.1 -n 2 > nul & del /f /q \"" + exe + "\"",
                WindowStyle = ProcessWindowStyle.Hidden,
                CreateNoWindow = true
            });
        }

        private static void ApplyFormIcon(Form form)
        {
            try
            {
                Icon icon = Icon.ExtractAssociatedIcon(Application.ExecutablePath);
                if (icon != null)
                {
                    form.Icon = icon;
                }
            }
            catch
            {
            }
        }
    }
}
