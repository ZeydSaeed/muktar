using System;
using System.Drawing;
using System.Management;
using System.Text;
using System.Windows.Forms;

namespace Muktar
{
    internal static class CpuReader
    {
        private const string Title = "Muktar";

        [STAThread]
        private static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            try
            {
                Application.Run(new CpuIdForm(ReadProcessorId()));
            }
            catch (Exception ex)
            {
                MessageBox.Show(
                    ex.Message,
                    Title,
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error,
                    MessageBoxDefaultButton.Button1,
                    MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
            }
        }

        private sealed class CpuIdForm : Form
        {
            public CpuIdForm(string code)
            {
                Text = Title;
                RightToLeft = RightToLeft.Yes;
                RightToLeftLayout = true;
                Font = new Font("Segoe UI", 10.5F, FontStyle.Regular, GraphicsUnit.Point);
                FormBorderStyle = FormBorderStyle.FixedDialog;
                MaximizeBox = false;
                MinimizeBox = false;
                StartPosition = FormStartPosition.CenterScreen;
                ClientSize = new Size(480, 260);
                BackColor = Color.White;
                ShowIcon = true;
                ApplyFormIcon(this);

                var header = new Panel
                {
                    Dock = DockStyle.Top,
                    Height = 64,
                    BackColor = Color.FromArgb(15, 76, 129),
                    Padding = new Padding(16)
                };
                header.Controls.Add(new Label
                {
                    Dock = DockStyle.Fill,
                    ForeColor = Color.White,
                    Font = new Font("Segoe UI", 13F, FontStyle.Bold, GraphicsUnit.Point),
                    TextAlign = ContentAlignment.MiddleCenter,
                    Text = "\u062a\u0641\u0639\u064a\u0644 \u0627\u0644\u0646\u0633\u062e\u0629"
                });

                var body = new TableLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    ColumnCount = 1,
                    RowCount = 4,
                    Padding = new Padding(24, 16, 24, 16)
                };
                body.RowStyles.Add(new RowStyle(SizeType.Absolute, 56));
                body.RowStyles.Add(new RowStyle(SizeType.Absolute, 40));
                body.RowStyles.Add(new RowStyle(SizeType.Percent, 100));
                body.RowStyles.Add(new RowStyle(SizeType.Absolute, 44));

                var hint = new Label
                {
                    Dock = DockStyle.Fill,
                    TextAlign = ContentAlignment.TopCenter,
                    Text = "\u0627\u0646\u0633\u062e \u0627\u0644\u0631\u0642\u0645 \u0623\u062f\u0646\u0627\u0647 \u0648\u0623\u0631\u0633\u0644\u0647 \u0644\u0644\u0628\u0627\u0626\u0639\r\n\u0644\u0625\u0643\u0645\u0627\u0644 \u062a\u0641\u0639\u064a\u0644 \u0646\u0633\u062e\u062a\u0643."
                };

                var codeBox = new TextBox
                {
                    Dock = DockStyle.Fill,
                    Font = new Font("Consolas", 13F, FontStyle.Bold, GraphicsUnit.Point),
                    TextAlign = HorizontalAlignment.Center,
                    ReadOnly = true,
                    Text = code,
                    RightToLeft = RightToLeft.No
                };

                var buttons = new FlowLayoutPanel
                {
                    Dock = DockStyle.Fill,
                    FlowDirection = FlowDirection.RightToLeft,
                    WrapContents = false
                };

                var copyButton = new Button
                {
                    Text = "\u0646\u0633\u062e",
                    Width = 120,
                    Height = 36,
                    FlatStyle = FlatStyle.System,
                    Margin = new Padding(8, 0, 0, 0)
                };
                copyButton.Click += (s, e) =>
                {
                    Clipboard.SetText(code);
                    MessageBox.Show(
                        "\u062a\u0645 \u0627\u0644\u0646\u0633\u062e.",
                        Title,
                        MessageBoxButtons.OK,
                        MessageBoxIcon.Information,
                        MessageBoxDefaultButton.Button1,
                        MessageBoxOptions.RtlReading | MessageBoxOptions.RightAlign);
                };

                var closeButton = new Button
                {
                    Text = "\u0625\u063a\u0644\u0627\u0642",
                    Width = 100,
                    Height = 36,
                    FlatStyle = FlatStyle.System
                };
                closeButton.Click += (s, e) => Close();

                buttons.Controls.Add(copyButton);
                buttons.Controls.Add(closeButton);

                body.Controls.Add(hint, 0, 0);
                body.Controls.Add(codeBox, 0, 1);
                body.Controls.Add(new Panel { Dock = DockStyle.Fill }, 0, 2);
                body.Controls.Add(buttons, 0, 3);

                Controls.Add(body);
                Controls.Add(header);

                AcceptButton = copyButton;
                CancelButton = closeButton;
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

                    var buffer = new StringBuilder();
                    foreach (char c in value.ToString().ToUpperInvariant())
                    {
                        if ((c >= 'A' && c <= 'Z') || (c >= '0' && c <= '9'))
                        {
                            buffer.Append(c);
                        }
                    }

                    if (buffer.Length >= 8)
                    {
                        return buffer.ToString();
                    }
                }
            }

            throw new InvalidOperationException(
                "\u062a\u0639\u0630\u0631 \u0642\u0631\u0627\u0621\u0629 \u0627\u0644\u0631\u0642\u0645.");
        }

        private static void ApplyFormIcon(Form form)
        {
            try
            {
                string exe = Application.ExecutablePath;
                Icon icon = Icon.ExtractAssociatedIcon(exe);
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
