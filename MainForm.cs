using System;
using System.Drawing;
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

        public MainForm()
        {
            InitializeComponent();
            InitializeUI();
        }

        private void InitializeComponent()
        {
            this.Text = "Network Tool Pro - Ultimate Networking Toolkit";
            this.Size = new Size(900, 650);
            this.StartPosition = FormStartPosition.CenterScreen;
            this.MinimumSize = new Size(800, 600);
            this.BackColor = Color.FromArgb(240, 240, 240);
        }

        private void InitializeUI()
        {
            Label titleLabel = new Label
            {
                Text = "🌐 Network Tool Pro",
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                ForeColor = Color.FromArgb(0, 122, 204),
                AutoSize = true,
                Location = new Point(20, 15)
            };
            this.Controls.Add(titleLabel);

            Label subtitleLabel = new Label
            {
                Text = "Professional Network Diagnostic & Configuration Tool",
                Font = new Font("Segoe UI", 9, FontStyle.Regular),
                ForeColor = Color.FromArgb(100, 100, 100),
                AutoSize = true,
                Location = new Point(20, 50)
            };
            this.Controls.Add(subtitleLabel);

            tabControl = new TabControl
            {
                Location = new Point(20, 80),
                Size = new Size(840, 520),
                Font = new Font("Segoe UI", 10, FontStyle.Regular)
            };

            portScannerTab = new TabPage("Port Scanner");
            ipInfoTab = new TabPage("IP Information");
            dnsConfigTab = new TabPage("DNS Configuration");

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
        }
    }
}
