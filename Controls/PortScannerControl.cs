using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;

namespace NetworkToolPro
{
    public class PortScannerControl : UserControl
    {
        private TextBox hostTextBox;
        private TextBox portsTextBox;
        private Button scanButton;
        private Button stopButton;
        private Button commonPortsButton;
        private ListView resultsListView;
        private ProgressBar progressBar;
        private Label statusLabel;
        private Label openPortsLabel;
        private Label closedPortsLabel;
        private NumericUpDown timeoutNumeric;
        private NumericUpDown concurrencyNumeric;
        
        private PortScannerService scannerService;
        private CancellationTokenSource? cts;

        public PortScannerControl()
        {
            scannerService = new PortScannerService();
            InitializeUI();
        }

        private void InitializeUI()
        {
            this.BackColor = Color.White;
            this.Padding = new Padding(20);

            var hostLabel = new Label
            {
                Text = "Target Host/IP:",
                Location = new Point(20, 20),
                Size = new Size(120, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(hostLabel);

            hostTextBox = new TextBox
            {
                Location = new Point(140, 20),
                Size = new Size(250, 23),
                Font = new Font("Segoe UI", 9),
                Text = "localhost"
            };
            this.Controls.Add(hostTextBox);

            var portsLabel = new Label
            {
                Text = "Ports (e.g., 80,443 or 20-100):",
                Location = new Point(20, 55),
                Size = new Size(200, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(portsLabel);

            portsTextBox = new TextBox
            {
                Location = new Point(220, 55),
                Size = new Size(170, 23),
                Font = new Font("Segoe UI", 9),
                Text = "20-100,443,8080"
            };
            this.Controls.Add(portsTextBox);

            commonPortsButton = new Button
            {
                Text = "Common Ports",
                Location = new Point(400, 53),
                Size = new Size(110, 27),
                BackColor = Color.FromArgb(240, 240, 240),
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 8)
            };
            commonPortsButton.Click += (s, e) =>
            {
                portsTextBox.Text = "21,22,23,25,53,80,110,135,139,143,443,445,3306,3389,5432,5900,8080,8443";
            };
            this.Controls.Add(commonPortsButton);

            var timeoutLabel = new Label
            {
                Text = "Timeout (ms):",
                Location = new Point(20, 90),
                Size = new Size(100, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(timeoutLabel);

            timeoutNumeric = new NumericUpDown
            {
                Location = new Point(120, 90),
                Size = new Size(80, 23),
                Minimum = 100,
                Maximum = 10000,
                Value = 1000,
                Increment = 100
            };
            this.Controls.Add(timeoutNumeric);

            var concurrencyLabel = new Label
            {
                Text = "Threads:",
                Location = new Point(220, 90),
                Size = new Size(70, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(concurrencyLabel);

            concurrencyNumeric = new NumericUpDown
            {
                Location = new Point(290, 90),
                Size = new Size(70, 23),
                Minimum = 1,
                Maximum = 500,
                Value = 100
            };
            this.Controls.Add(concurrencyNumeric);

            scanButton = new Button
            {
                Text = "🔍 Start Scan",
                Location = new Point(400, 85),
                Size = new Size(110, 32),
                BackColor = Color.FromArgb(0, 122, 204),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            scanButton.FlatAppearance.BorderSize = 0;
            scanButton.Click += async (s, e) => await StartScanAsync();
            this.Controls.Add(scanButton);

            stopButton = new Button
            {
                Text = "⏹ Stop",
                Location = new Point(520, 85),
                Size = new Size(80, 32),
                BackColor = Color.FromArgb(220, 53, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                Enabled = false
            };
            stopButton.FlatAppearance.BorderSize = 0;
            stopButton.Click += (s, e) => StopScan();
            this.Controls.Add(stopButton);

            resultsListView = new ListView
            {
                Location = new Point(20, 160),
                Size = new Size(760, 250),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Consolas", 9)
            };
            resultsListView.Columns.Add("Port", 80);
            resultsListView.Columns.Add("Status", 100);
            resultsListView.Columns.Add("Response Time", 120);
            resultsListView.Columns.Add("Service", 150);
            resultsListView.Columns.Add("Notes", 300);
            this.Controls.Add(resultsListView);

            progressBar = new ProgressBar
            {
                Location = new Point(20, 125),
                Size = new Size(580, 23),
                Style = ProgressBarStyle.Continuous
            };
            this.Controls.Add(progressBar);

            statusLabel = new Label
            {
                Text = "Ready to scan",
                Location = new Point(610, 125),
                Size = new Size(170, 23),
                Font = new Font("Segoe UI", 8),
                TextAlign = ContentAlignment.MiddleRight
            };
            this.Controls.Add(statusLabel);

            openPortsLabel = new Label
            {
                Text = "✓ Open Ports: 0",
                Location = new Point(20, 420),
                Size = new Size(150, 23),
                Font = new Font("Segoe UI", 10, FontStyle.Bold),
                ForeColor = Color.FromArgb(40, 167, 69)
            };
            this.Controls.Add(openPortsLabel);

            closedPortsLabel = new Label
            {
                Text = "✗ Closed Ports: 0",
                Location = new Point(180, 420),
                Size = new Size(150, 23),
                Font = new Font("Segoe UI", 10, FontStyle.Bold),
                ForeColor = Color.FromArgb(220, 53, 69)
            };
            this.Controls.Add(closedPortsLabel);
        }

        private async Task StartScanAsync()
        {
            var host = hostTextBox.Text.Trim();
            if (string.IsNullOrWhiteSpace(host))
            {
                MessageBox.Show("Please enter a host or IP address.", "Validation Error", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var ports = ParsePorts(portsTextBox.Text);
            if (ports.Count == 0)
            {
                MessageBox.Show("Please enter valid ports.", "Validation Error", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            resultsListView.Items.Clear();
            progressBar.Value = 0;
            progressBar.Maximum = ports.Count;
            scanButton.Enabled = false;
            stopButton.Enabled = true;

            cts = new CancellationTokenSource();

            try
            {
                var progress = new Progress<PortScanProgress>(p =>
                {
                    if (this.InvokeRequired)
                    {
                        this.Invoke(() => UpdateProgress(p));
                    }
                    else
                    {
                        UpdateProgress(p);
                    }
                });

                var timeout = TimeSpan.FromMilliseconds((double)timeoutNumeric.Value);
                var concurrency = (int)concurrencyNumeric.Value;

                var results = await scannerService.ScanAsync(host, ports, timeout, concurrency, progress, cts.Token);

                MessageBox.Show($"Scan completed!\n\nTotal ports: {results.Count}\nOpen: {results.Count(r => r.Status == PortStatus.Open)}\nClosed: {results.Count(r => r.Status == PortStatus.Closed)}",
                    "Scan Complete", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            catch (OperationCanceledException)
            {
                statusLabel.Text = "Scan cancelled";
            }
            catch (Exception ex)
            {
                MessageBox.Show($"Error during scan: {ex.Message}", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                scanButton.Enabled = true;
                stopButton.Enabled = false;
                cts?.Dispose();
                cts = null;
            }
        }

        private void UpdateProgress(PortScanProgress progress)
        {
            if (progressBar.Maximum > 0)
            {
                progressBar.Value = Math.Min(progress.PortsCompleted, progressBar.Maximum);
            }

            statusLabel.Text = progress.TotalElapsed.HasValue
                ? $"Completed {progress.PortsCompleted}/{progress.TotalPorts} ports in {progress.TotalElapsed.Value.TotalSeconds:F1}s"
                : $"{progress.PortsCompleted}/{progress.TotalPorts}";

            if (progress.LatestResult != null)
            {
                var item = new ListViewItem(progress.LatestResult.Port.ToString());
                item.SubItems.Add(progress.LatestResult.Status.ToString());
                item.SubItems.Add($"{progress.LatestResult.ResponseTime.TotalMilliseconds:F0} ms");
                item.SubItems.Add(GetServiceName(progress.LatestResult.Port));
                item.SubItems.Add(progress.LatestResult.Notes);

                if (progress.LatestResult.Status == PortStatus.Open)
                {
                    item.BackColor = Color.FromArgb(212, 237, 218);
                    item.ForeColor = Color.FromArgb(21, 87, 36);
                }
                else
                {
                    item.ForeColor = Color.Gray;
                }

                resultsListView.Items.Add(item);
                resultsListView.EnsureVisible(resultsListView.Items.Count - 1);
            }

            var openCount = resultsListView.Items.Cast<ListViewItem>().Count(i => i.SubItems[1].Text == "Open");
            var closedCount = resultsListView.Items.Cast<ListViewItem>().Count(i => i.SubItems[1].Text == "Closed");

            openPortsLabel.Text = $"✓ Open Ports: {openCount}";
            closedPortsLabel.Text = $"✗ Closed Ports: {closedCount}";
        }

        private void StopScan()
        {
            if (cts == null)
            {
                return;
            }

            stopButton.Enabled = false;
            cts.Cancel();
            statusLabel.Text = "Cancelling...";
        }

        private List<int> ParsePorts(string input)
        {
            var ports = new HashSet<int>();
            var parts = input.Split(',', ';');

            foreach (var part in parts)
            {
                var trimmed = part.Trim();
                if (trimmed.Contains('-'))
                {
                    var range = trimmed.Split('-');
                    if (range.Length == 2 && int.TryParse(range[0], out var start) && int.TryParse(range[1], out var end))
                    {
                        for (int i = Math.Max(1, start); i <= Math.Min(65535, end); i++)
                        {
                            ports.Add(i);
                        }
                    }
                }
                else if (int.TryParse(trimmed, out var port) && port >= 1 && port <= 65535)
                {
                    ports.Add(port);
                }
            }

            return ports.OrderBy(p => p).ToList();
        }

        private string GetServiceName(int port) => port switch
        {
            20 => "FTP Data",
            21 => "FTP",
            22 => "SSH",
            23 => "Telnet",
            25 => "SMTP",
            53 => "DNS",
            80 => "HTTP",
            110 => "POP3",
            135 => "RPC",
            139 => "NetBIOS",
            143 => "IMAP",
            443 => "HTTPS",
            445 => "SMB",
            3306 => "MySQL",
            3389 => "RDP",
            5432 => "PostgreSQL",
            5900 => "VNC",
            8080 => "HTTP Proxy",
            8443 => "HTTPS Alt",
            _ => ""
        };
    }
}
