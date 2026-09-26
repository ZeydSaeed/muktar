using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Text;
using System.Windows.Forms;

namespace Muktar
{
    internal static class Binder
    {
        private const string Title = "Muktar Binder";
        private const string OpenMarker = "MUKTAR_PID<<";
        private const string CloseMarker = ">>MUKTAR_PID";
        private const int SlotLength = 64;

        [STAThread]
        private static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new BinderForm());
        }

        private sealed class BinderForm : Form
        {
            private readonly TextBox customerBox;
            private readonly TextBox idBox;
            private readonly TextBox sourceBox;
            private readonly TextBox outputBox;
            private readonly Label statusLabel;
            private readonly string defaultOutDir;

            public BinderForm()
            {
                Text = Title;
                RightToLeft = RightToLeft.Yes;
                RightToLeftLayout = true;
                Font = new Font("Segoe UI", 10F, FontStyle.Regular, GraphicsUnit.Point);
                FormBorderStyle = FormBorderStyle.FixedDialog;
                MaximizeBox = false;
                MinimizeBox = false;
                StartPosition = FormStartPosition.CenterScreen;
                ClientSize = new Size(620, 520);
                MinimumSize = new Size(640, 560);
                BackColor = Color.White;
                AutoScaleMode = AutoScaleMode.Dpi;
                ShowIcon = true;
                ApplyFormIcon(this);

                string defaultSource = FindDefaultSetup();
                defaultOutDir = string.IsNullOrEmpty(defaultSource)
                    ? Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory)
                    : Path.Combine(Path.GetDirectoryName(defaultSource) ?? "", "customers");

                var header = new Panel
                {
                    Dock = DockStyle.Top,
                    Height = 60,
                    BackColor = Color.FromArgb(15, 76, 129),
                    Padding = new Padding(16)
                };
                header.Controls.Add(new Label
                {
                    Dock = DockStyle.Fill,
                    ForeColor = Color.White,
                    Font = new Font("Segoe UI", 12.5F, FontStyle.Bold, GraphicsUnit.Point),
                    TextAlign = ContentAlignment.MiddleCenter,
                    Text = "\u0625\u0646\u0634\u0627\u0621 \u0646\u0633\u062e\u0629 \u0632\u0628\u0648\u0646"
                });

                var root = new TableLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    ColumnCount = 1,
                    RowCount = 11,
                    Padding = new Padding(18, 12, 18, 12)
                };
                root.ColumnStyles.Add(new ColumnStyle(SizeType.Percent, 100));
                float[] rows = { 22, 30, 22, 30, 22, 30, 28, 22, 30, 28, 44 };
                for (int i = 0; i < rows.Length; i++)
                {
                    root.RowStyles.Add(new RowStyle(SizeType.Absolute, rows[i] + (i == 10 ? 0 : 4)));
                }
                root.RowStyles[10] = new RowStyle(SizeType.Percent, 100);

                root.Controls.Add(MakeLabel("1) \u0627\u0633\u0645 \u0627\u0644\u0632\u0628\u0648\u0646"), 0, 0);
                customerBox = MakeTextBox("");
                root.Controls.Add(customerBox, 0, 1);

                root.Controls.Add(MakeLabel("2) \u0627\u0644\u0631\u0642\u0645 \u0627\u0644\u0630\u064a \u0623\u0631\u0633\u0644\u0647 \u0627\u0644\u0632\u0628\u0648\u0646"), 0, 2);
                idBox = MakeTextBox("");
                idBox.RightToLeft = RightToLeft.No;
                idBox.Font = new Font("Consolas", 11F, FontStyle.Bold, GraphicsUnit.Point);
                idBox.TextAlign = HorizontalAlignment.Center;
                root.Controls.Add(idBox, 0, 3);

                root.Controls.Add(MakeLabel("3) \u0645\u0644\u0641 MuktarSetup.exe \u0627\u0644\u0623\u0633\u0627\u0633\u064a"), 0, 4);
                sourceBox = MakeTextBox(defaultSource);
                sourceBox.RightToLeft = RightToLeft.No;
                root.Controls.Add(sourceBox, 0, 5);

                var sourceButtons = new FlowLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    FlowDirection = FlowDirection.RightToLeft,
                    WrapContents = false
                };
                var browseSource = MakeButton("\u0627\u0633\u062a\u0639\u0631\u0627\u0636...", 120);
                browseSource.Click += (s, e) => BrowseSource();
                sourceButtons.Controls.Add(browseSource);
                root.Controls.Add(sourceButtons, 0, 6);

                root.Controls.Add(MakeLabel("4) \u0645\u0644\u0641 \u0627\u0644\u0625\u062e\u0631\u0627\u062c"), 0, 7);
                outputBox = MakeTextBox("");
                outputBox.RightToLeft = RightToLeft.No;
                root.Controls.Add(outputBox, 0, 8);

                var outButtons = new FlowLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    FlowDirection = FlowDirection.RightToLeft,
                    WrapContents = false
                };
                var browseOut = MakeButton("\u062d\u0641\u0638 \u0628\u0627\u0633\u0645...", 120);
                browseOut.Click += (s, e) => BrowseOutput();
                outButtons.Controls.Add(browseOut);
                root.Controls.Add(outButtons, 0, 9);

                var bottom = new TableLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    ColumnCount = 1,
                    RowCount = 2,
                    Padding = new Padding(0, 6, 0, 0)
                };
                bottom.RowStyles.Add(new RowStyle(SizeType.Percent, 100));
                bottom.RowStyles.Add(new RowStyle(SizeType.Absolute, 44));

                statusLabel = new Label
                {
                    Dock = DockStyle.Fill,
                    TextAlign = ContentAlignment.TopRight,
                    ForeColor = Color.FromArgb(55, 55, 55),
                    Text = "\u0627\u0644\u0645\u0644\u0641 \u0627\u0644\u0623\u0633\u0627\u0633\u064a \u064a\u0628\u0642\u0649 \u062b\u0627\u0628\u062a\u064b\u0627. \u064a\u064f\u0646\u0634\u0623 \u0645\u0644\u0641 \u0628\u0627\u0633\u0645 \u0627\u0644\u0632\u0628\u0648\u0646 \u0641\u0642\u0637."
                };

                var actions = new FlowLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    FlowDirection = FlowDirection.RightToLeft,
                    WrapContents = false
                };
                var injectButton = MakeButton("\u062d\u0642\u0646 \u0648\u0625\u0646\u0634\u0627\u0621 \u0627\u0644\u0645\u0644\u0641", 180);
                injectButton.Click += (s, e) => Inject();
                var openFolder = MakeButton("\u0641\u062a\u062d \u0627\u0644\u0645\u062c\u0644\u062f", 130);
                openFolder.Click += (s, e) => OpenOutputFolder();
                actions.Controls.Add(injectButton);
                actions.Controls.Add(openFolder);

                bottom.Controls.Add(statusLabel, 0, 0);
                bottom.Controls.Add(actions, 0, 1);
                root.Controls.Add(bottom, 0, 10);

                Controls.Add(root);
                Controls.Add(header);

                customerBox.TextChanged += (s, e) => SuggestOutput();
                idBox.TextChanged += (s, e) => SuggestOutput();

                if (!string.IsNullOrEmpty(defaultOutDir) && !Directory.Exists(defaultOutDir))
                {
                    try { Directory.CreateDirectory(defaultOutDir); } catch { }
                }

                SuggestOutput();
            }

            private static Label MakeLabel(string text)
            {
                return new Label
                {
                    Dock = DockStyle.Fill,
                    TextAlign = ContentAlignment.MiddleRight,
                    Text = text
                };
            }

            private static TextBox MakeTextBox(string text)
            {
                return new TextBox
                {
                    Dock = DockStyle.Fill,
                    Text = text ?? string.Empty
                };
            }

            private static Button MakeButton(string text, int width)
            {
                return new Button
                {
                    Text = text,
                    Width = width,
                    Height = 34,
                    FlatStyle = FlatStyle.System,
                    Margin = new Padding(6, 0, 0, 0)
                };
            }

            private void BrowseSource()
            {
                using (var dialog = new OpenFileDialog())
                {
                    dialog.Filter = "MuktarSetup|MuktarSetup.exe|EXE|*.exe";
                    dialog.Title = "MuktarSetup.exe";
                    if (!string.IsNullOrWhiteSpace(sourceBox.Text) && File.Exists(sourceBox.Text))
                    {
                        dialog.InitialDirectory = Path.GetDirectoryName(sourceBox.Text);
                    }
                    if (dialog.ShowDialog(this) == DialogResult.OK)
                    {
                        sourceBox.Text = dialog.FileName;
                        SuggestOutput();
                    }
                }
            }

            private void BrowseOutput()
            {
                using (var dialog = new SaveFileDialog())
                {
                    dialog.Filter = "EXE|*.exe";
                    dialog.FileName = SuggestFileName();
                    dialog.InitialDirectory = Directory.Exists(defaultOutDir)
                        ? defaultOutDir
                        : Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
                    if (dialog.ShowDialog(this) == DialogResult.OK)
                    {
                        outputBox.Text = dialog.FileName;
                    }
                }
            }

            private void OpenOutputFolder()
            {
                string path = outputBox.Text.Trim();
                string dir = File.Exists(path)
                    ? Path.GetDirectoryName(path)
                    : (Directory.Exists(defaultOutDir) ? defaultOutDir : Path.GetDirectoryName(sourceBox.Text));
                if (!string.IsNullOrEmpty(dir) && Directory.Exists(dir))
                {
                    Process.Start("explorer.exe", dir);
                }
            }

            private void SuggestOutput()
            {
                string source = sourceBox.Text.Trim();
                if (string.IsNullOrEmpty(source) || !File.Exists(source))
                {
                    return;
                }

                string dir = Path.Combine(Path.GetDirectoryName(source) ?? "", "customers");
                try { Directory.CreateDirectory(dir); } catch { }
                outputBox.Text = Path.Combine(dir, SuggestFileName());
            }

            private string SuggestFileName()
            {
                string customer = SanitizeFileName(customerBox.Text.Trim());
                if (string.IsNullOrEmpty(customer))
                {
                    return "MuktarSetup-Customer.exe";
                }

                return "MuktarSetup-" + customer + ".exe";
            }

            private void Inject()
            {
                try
                {
                    string source = sourceBox.Text.Trim();
                    string output = outputBox.Text.Trim();
                    string id = NormalizeId(idBox.Text);
                    string customer = customerBox.Text.Trim();

                    if (string.IsNullOrWhiteSpace(customer))
                    {
                        throw new InvalidOperationException("\u0623\u062f\u062e\u0644 \u0627\u0633\u0645 \u0627\u0644\u0632\u0628\u0648\u0646.");
                    }

                    if (string.IsNullOrEmpty(SanitizeFileName(customer)))
                    {
                        throw new InvalidOperationException("\u0627\u0633\u0645 \u0627\u0644\u0632\u0628\u0648\u0646 \u063a\u064a\u0631 \u0635\u0627\u0644\u062d \u0644\u0627\u0633\u0645 \u0627\u0644\u0645\u0644\u0641.");
                    }

                    if (string.IsNullOrEmpty(id) || id.Length < 8)
                    {
                        throw new InvalidOperationException(
                            "\u0623\u062f\u062e\u0644 \u0627\u0644\u0631\u0642\u0645 \u0627\u0644\u0630\u064a \u0623\u0631\u0633\u0644\u0647 \u0627\u0644\u0632\u0628\u0648\u0646 (\u0639\u0644\u0649 \u0627\u0644\u0623\u0642\u0644 8 \u0623\u062d\u0631\u0641).");
                    }

                    if (id.Length > SlotLength)
                    {
                        throw new InvalidOperationException("\u0627\u0644\u0631\u0642\u0645 \u0623\u0637\u0648\u0644 \u0645\u0646 \u0627\u0644\u0645\u0633\u0645\u0648\u062d.");
                    }

                    if (!File.Exists(source))
                    {
                        throw new InvalidOperationException("\u0645\u0644\u0641 MuktarSetup.exe \u063a\u064a\u0631 \u0645\u0648\u062c\u0648\u062f.");
                    }

                    if (string.IsNullOrWhiteSpace(output))
                    {
                        SuggestOutput();
                        output = outputBox.Text.Trim();
                    }

                    // Always prefer customer name in filename if user left default with code.
                    string desiredName = SuggestFileName();
                    string outDir = Path.GetDirectoryName(output);
                    if (string.IsNullOrEmpty(outDir))
                    {
                        outDir = defaultOutDir;
                    }
                    output = Path.Combine(outDir, desiredName);
                    outputBox.Text = output;

                    if (string.Equals(
                        Path.GetFullPath(source),
                        Path.GetFullPath(output),
                        StringComparison.OrdinalIgnoreCase))
                    {
                        throw new InvalidOperationException(
                            "\u0644\u0627 \u062a\u062d\u0642\u0646 \u0639\u0644\u0649 \u0627\u0644\u0645\u0644\u0641 \u0627\u0644\u0623\u0633\u0627\u0633\u064a. \u0627\u062e\u062a\u0631 \u0645\u062c\u0644\u062f customers.");
                    }

                    Directory.CreateDirectory(outDir);

                    byte[] bytes = File.ReadAllBytes(source);
                    PatchProcessorId(bytes, id);
                    File.WriteAllBytes(output, bytes);

                    statusLabel.ForeColor = Color.FromArgb(0, 120, 60);
                    statusLabel.Text = "\u062a\u0645 \u0627\u0644\u0625\u0646\u0634\u0627\u0621:\n" + output;

                    MessageBox.Show(
                        "\u062a\u0645 \u0625\u0646\u0634\u0627\u0621 \u0645\u0644\u0641 \u0627\u0644\u0632\u0628\u0648\u0646:\n" +
                        desiredName + "\n\n" + output,
                        Title,
                        MessageBoxButtons.OK,
                        MessageBoxIcon.Information,
                        MessageBoxDefaultButton.Button1,
                        MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
                }
                catch (Exception ex)
                {
                    statusLabel.ForeColor = Color.DarkRed;
                    statusLabel.Text = ex.Message;
                    MessageBox.Show(
                        ex.Message,
                        Title,
                        MessageBoxButtons.OK,
                        MessageBoxIcon.Error,
                        MessageBoxDefaultButton.Button1,
                        MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
                }
            }
        }

        private static string SanitizeFileName(string value)
        {
            if (string.IsNullOrWhiteSpace(value))
            {
                return string.Empty;
            }

            var invalid = Path.GetInvalidFileNameChars();
            var buffer = new StringBuilder();
            foreach (char c in value.Trim())
            {
                if (Array.IndexOf(invalid, c) >= 0 || c < 32)
                {
                    buffer.Append('-');
                }
                else
                {
                    buffer.Append(c);
                }
            }

            string cleaned = buffer.ToString().Trim().Trim('.');
            while (cleaned.Contains("--"))
            {
                cleaned = cleaned.Replace("--", "-");
            }
            return cleaned;
        }

        private static string FindDefaultSetup()
        {
            string baseDir = AppDomain.CurrentDomain.BaseDirectory.TrimEnd(Path.DirectorySeparatorChar);
            string beside = Path.Combine(baseDir, "MuktarSetup.exe");
            if (File.Exists(beside))
            {
                return beside;
            }

            string up = Path.GetFullPath(Path.Combine(baseDir, "..", "dist", "MuktarSetup.exe"));
            if (File.Exists(up))
            {
                return up;
            }

            return beside;
        }

        private static void PatchProcessorId(byte[] bytes, string processorId)
        {
            byte[] open = Encoding.Unicode.GetBytes(OpenMarker);
            byte[] close = Encoding.Unicode.GetBytes(CloseMarker);
            int openPos = IndexOf(bytes, open);
            if (openPos < 0)
            {
                throw new InvalidOperationException(
                    "\u0644\u0645 \u064a\u062a\u0645 \u0627\u0644\u0639\u062b\u0648\u0631 \u0639\u0644\u0649 \u062e\u0627\u0646\u0629 \u0627\u0644\u062d\u0642\u0646. \u0623\u0639\u062f \u0628\u0646\u0627\u0621 MuktarSetup.exe.");
            }

            int slotStart = openPos + open.Length;
            int closePos = IndexOf(bytes, close, slotStart);
            int slotBytes = SlotLength * 2;
            if (closePos < 0 || closePos - slotStart != slotBytes)
            {
                throw new InvalidOperationException("\u062e\u0627\u0646\u0629 \u0627\u0644\u062d\u0642\u0646 \u062a\u0627\u0644\u0641\u0629.");
            }

            for (int i = 0; i < SlotLength; i++)
            {
                bytes[slotStart + (i * 2)] = (byte)'_';
                bytes[slotStart + (i * 2) + 1] = 0;
            }

            string upper = processorId.ToUpperInvariant();
            for (int i = 0; i < upper.Length; i++)
            {
                bytes[slotStart + (i * 2)] = (byte)upper[i];
                bytes[slotStart + (i * 2) + 1] = 0;
            }
        }

        private static int IndexOf(byte[] haystack, byte[] needle, int start = 0)
        {
            for (int i = start; i <= haystack.Length - needle.Length; i++)
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
