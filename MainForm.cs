using System;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace NetworkToolPro
{
    public partial class MainForm : Form
    {
        private TabControl tabControl;
        private TabPage portScannerTab;
        private TabPage ipInfoTab;
        private TabPage dnsConfigTab;
        
        private PortScannerControl portScannerControl;
        private IPInfoControl ipInfoControl;
        private DNSConfigControl dnsConfigControl;

        private Panel headerPanel;
        private Panel footerPanel;
        private Label versionLabel;
        private Label statusBarLabel;
        private Label copyrightLabel;

        public MainForm()
        {
            InitializeComponent();
            InitializeUI();
            ApplyModernStyling();
        }

        private void InitializeComponent()
        {
            this.Text = "Network Tool Pro - Ultimate Networking Toolkit";
            this.Size = new Size(1100, 750);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.MinimumSize = new Size(900, 650);
            this.BackColor = Color.FromArgb(250, 250, 252);
            this.FormBorderStyle = FormBorderStyle.Sizable;
            this.Icon = CreateAppIcon();
        }

        private Icon CreateAppIcon()
        {
            Bitmap bitmap = new Bitmap(32, 32);
            using (Graphics g = Graphics.FromImage(bitmap))
            {
                g.SmoothingMode = SmoothingMode.AntiAlias;
                using (LinearGradientBrush brush = new LinearGradientBrush(
                    new Rectangle(0, 0, 32, 32),
                    Color.FromArgb(99, 102, 241),
                    Color.FromArgb(59, 130, 246),
                    45f))
                {
                    g.FillEllipse(brush, 4, 4, 24, 24);
                }
                using (Pen pen = new Pen(Color.White, 2))
                {
                    g.DrawLine(pen, 10, 16, 16, 16);
                    g.DrawLine(pen, 16, 10, 16, 22);
                }
            }
            return Icon.FromHandle(bitmap.GetHicon());
        }

        private void InitializeUI()
        {
            headerPanel = new Panel
            {
                Height = 100,
                Dock = DockStyle.Top,
                BackColor = Color.White
            };
            headerPanel.Paint += HeaderPanel_Paint;

            Label titleLabel = new Label
            {
                Text = "🌐 Network Tool Pro",
                Font = new Font("Segoe UI", 24, FontStyle.Bold),
                ForeColor = Color.FromArgb(30, 41, 59),
                AutoSize = true,
                Location = new Point(30, 20)
            };
            headerPanel.Controls.Add(titleLabel);

            Label subtitleLabel = new Label
            {
                Text = "Professional Network Diagnostic & Configuration Tool",
                Font = new Font("Segoe UI", 10, FontStyle.Regular),
                ForeColor = Color.FromArgb(100, 116, 139),
                AutoSize = true,
                Location = new Point(30, 58)
            };
            headerPanel.Controls.Add(subtitleLabel);

            versionLabel = new Label
            {
                Text = "v1.0.0",
                Font = new Font("Segoe UI", 9, FontStyle.Regular),
                ForeColor = Color.FromArgb(148, 163, 184),
                AutoSize = true,
                Anchor = AnchorStyles.Top | AnchorStyles.Right,
                Location = new Point(this.ClientSize.Width - 80, 25)
            };
            headerPanel.Controls.Add(versionLabel);

            this.Controls.Add(headerPanel);

            tabControl = new TabControl
            {
                Dock = DockStyle.Fill,
                Font = new Font("Segoe UI", 10, FontStyle.Regular),
                Padding = new Point(20, 8)
            };
            tabControl.DrawMode = TabDrawMode.OwnerDrawFixed;
            tabControl.DrawItem += TabControl_DrawItem;

            portScannerTab = new TabPage("🔍 Port Scanner");
            ipInfoTab = new TabPage("📊 IP Information");
            dnsConfigTab = new TabPage("⚙️ DNS Configuration");

            portScannerControl = new PortScannerControl { Dock = DockStyle.Fill };
            ipInfoControl = new IPInfoControl { Dock = DockStyle.Fill };
            dnsConfigControl = new DNSConfigControl { Dock = DockStyle.Fill };

            portScannerTab.Controls.Add(portScannerControl);
            ipInfoTab.Controls.Add(ipInfoControl);
            dnsConfigTab.Controls.Add(dnsConfigControl);

            tabControl.TabPages.Add(portScannerTab);
            tabControl.TabPages.Add(ipInfoTab);
            tabControl.TabPages.Add(dnsConfigTab);

            this.Controls.Add(tabControl);

            footerPanel = new Panel
            {
                Height = 35,
                Dock = DockStyle.Bottom,
                BackColor = Color.FromArgb(248, 250, 252)
            };

            statusBarLabel = new Label
            {
                Text = "Ready",
                Font = new Font("Segoe UI", 9, FontStyle.Regular),
                ForeColor = Color.FromArgb(100, 116, 139),
                AutoSize = false,
                Size = new Size(800, 30),
                Location = new Point(15, 8),
                Anchor = AnchorStyles.Left | AnchorStyles.Bottom,
                TextAlign = ContentAlignment.MiddleLeft
            };
            footerPanel.Controls.Add(statusBarLabel);

            copyrightLabel = new Label
            {
                Text = "© 2024 Network Tool Pro",
                Font = new Font("Segoe UI", 9, FontStyle.Regular),
                ForeColor = Color.FromArgb(148, 163, 184),
                AutoSize = true,
                Anchor = AnchorStyles.Right | AnchorStyles.Bottom
            };
            copyrightLabel.Location = new Point(this.ClientSize.Width - copyrightLabel.Width - 15, 8);
            footerPanel.Controls.Add(copyrightLabel);

            this.Controls.Add(footerPanel);
        }

        private void HeaderPanel_Paint(object sender, PaintEventArgs e)
        {
            using (Pen pen = new Pen(Color.FromArgb(226, 232, 240), 1))
            {
                e.Graphics.DrawLine(pen, 0, headerPanel.Height - 1, headerPanel.Width, headerPanel.Height - 1);
            }
        }

        private void TabControl_DrawItem(object sender, DrawItemEventArgs e)
        {
            Graphics g = e.Graphics;
            g.SmoothingMode = SmoothingMode.AntiAlias;

            TabPage tabPage = tabControl.TabPages[e.Index];
            Rectangle tabRect = tabControl.GetTabRect(e.Index);

            bool isSelected = (e.Index == tabControl.SelectedIndex);

            using (SolidBrush bgBrush = new SolidBrush(isSelected ? Color.White : Color.FromArgb(241, 245, 249)))
            {
                g.FillRectangle(bgBrush, tabRect);
            }

            if (isSelected)
            {
                using (Pen borderPen = new Pen(Color.FromArgb(99, 102, 241), 3))
                {
                    g.DrawLine(borderPen, tabRect.Left, tabRect.Bottom - 2, tabRect.Right, tabRect.Bottom - 2);
                }
            }

            Color textColor = isSelected ? Color.FromArgb(99, 102, 241) : Color.FromArgb(100, 116, 139);
            using (SolidBrush textBrush = new SolidBrush(textColor))
            {
                StringFormat stringFormat = new StringFormat
                {
                    Alignment = StringAlignment.Center,
                    LineAlignment = StringAlignment.Center
                };
                g.DrawString(tabPage.Text, tabControl.Font, textBrush, tabRect, stringFormat);
            }
        }

        private void ApplyModernStyling()
        {
            foreach (TabPage tab in tabControl.TabPages)
            {
                tab.BackColor = Color.FromArgb(250, 250, 252);
                tab.Padding = new Padding(15);
            }

            this.Resize += (s, e) =>
            {
                versionLabel.Location = new Point(Math.Max(30, this.ClientSize.Width - versionLabel.Width - 30), 25);
                copyrightLabel.Location = new Point(Math.Max(30, this.ClientSize.Width - copyrightLabel.Width - 30), 8);
            };

            versionLabel.Location = new Point(Math.Max(30, this.ClientSize.Width - versionLabel.Width - 30), 25);
            copyrightLabel.Location = new Point(Math.Max(30, this.ClientSize.Width - copyrightLabel.Width - 30), 8);
        }

        public void UpdateStatusBar(string message)
        {
            if (statusBarLabel.InvokeRequired)
            {
                statusBarLabel.Invoke(new Action(() => statusBarLabel.Text = message));
            }
            else
            {
                statusBarLabel.Text = message;
            }
        }
    }
}
