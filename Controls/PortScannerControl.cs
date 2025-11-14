using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.IO;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;
using NetworkToolPro.UI;

namespace NetworkToolPro
{
    public class PortScannerControl : UserControl
    {
        private Panel configPanel;
        private Panel resultsPanel;
        private Panel statsPanel;
        
        private TextBox hostTextBox;
        private TextBox portsTextBox;
        private Button scanButton;
        private Button stopButton;
        private Button commonPortsButton;
        private Button exportButton;
        private ListView resultsListView;
        private ProgressBar progressBar;
        private Label statusLabel;
        private Panel openPortsCard;
        private Panel closedPortsCard;
        private Panel totalScannedCard;
        private Panel scanTimeCard;
        private NumericUpDown timeoutNumeric;
        private NumericUpDown concurrencyNumeric;
        
        private PortScannerService scannerService;
        private CancellationTokenSource? cts;
        private DateTime scanStartTime;

        public PortScannerControl()
        {
            scannerService = new PortScannerService();
            this.Dock = DockStyle.Fill;
            this.AutoScroll = true;
            InitializeUI();
        }

        private void InitializeUI()
        {
            this.BackColor = ThemeManager.BackgroundPrimary;
            this.Padding = new Padding(20);

            configPanel = CreateConfigurationPanel();
            configPanel.Location = new Point(20, 20);
            configPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(configPanel);

            statsPanel = CreateStatsPanel();
            statsPanel.Location = new Point(20, configPanel.Bottom + 15);
            statsPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(statsPanel);

            resultsPanel = CreateResultsPanel();
            resultsPanel.Location = new Point(20, statsPanel.Bottom + 15);
            resultsPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom;
            this.Controls.Add(resultsPanel);

            this.SizeChanged += (s, e) => UpdateLayoutSizes();
            UpdateLayoutSizes();
        }

        private void UpdateLayoutSizes()
        {
            int width = Math.Max(600, this.ClientSize.Width - 40);
            configPanel.Size = new Size(width, 260);
            configPanel.Location = new Point(20, 20);

            statsPanel.Size = new Size(width, 120);
            statsPanel.Location = new Point(20, configPanel.Bottom + 15);

            resultsPanel.Size = new Size(width, Math.Max(200, this.ClientSize.Height - (statsPanel.Bottom + 35)));
            resultsPanel.Location = new Point(20, statsPanel.Bottom + 15);
        }

        private Panel CreateConfigurationPanel()
        {
            var panel = new Panel
            {
                Size = new Size(760, 260),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(25)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "⚙️ Configuration",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(25, 20),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            var hostLabel = new Label
            {
                Text = "Target Host / IP Address:",
                Location = new Point(25, 65),
                Size = new Size(200, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(hostLabel);

            hostTextBox = new TextBox
            {
                Location = new Point(25, 90),
                Size = new Size(350, 28),
                Font = new Font("Segoe UI", 10),
                Text = "localhost",
                BorderStyle = BorderStyle.FixedSingle
            };
            panel.Controls.Add(hostTextBox);

            var portsLabel = new Label
            {
                Text = "Ports (e.g., 80,443 or 20-100):",
                Location = new Point(390, 65),
                Size = new Size(220, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(portsLabel);

            portsTextBox = new TextBox
            {
                Location = new Point(390, 90),
                Size = new Size(250, 28),
                Font = new Font("Segoe UI", 10),
                Text = "20-100,443,8080",
                BorderStyle = BorderStyle.FixedSingle
            };
            panel.Controls.Add(portsTextBox);

            commonPortsButton = CreateStyledButton("📋 Common Ports", new Point(655, 88), new Size(140, 32));
            commonPortsButton.BackColor = Color.FromArgb(241, 245, 249);
            commonPortsButton.ForeColor = ThemeManager.TextSecondary;
            commonPortsButton.Click += (s, e) =>
            {
                portsTextBox.Text = "21,22,23,25,53,80,110,135,139,143,443,445,3306,3389,5432,5900,8080,8443";
            };
            panel.Controls.Add(commonPortsButton);

            var timeoutLabel = new Label
            {
                Text = "Timeout (ms):",
                Location = new Point(25, 135),
                Size = new Size(100, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(timeoutLabel);

            timeoutNumeric = new NumericUpDown
            {
                Location = new Point(25, 160),
                Size = new Size(100, 28),
                Minimum = 100,
                Maximum = 10000,
                Value = 1000,
                Increment = 100,
                Font = new Font("Segoe UI", 10)
            };
            panel.Controls.Add(timeoutNumeric);

            var concurrencyLabel = new Label
            {
                Text = "Concurrency:",
                Location = new Point(145, 135),
                Size = new Size(100, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(concurrencyLabel);

            concurrencyNumeric = new NumericUpDown
            {
                Location = new Point(145, 160),
                Size = new Size(100, 28),
                Minimum = 1,
                Maximum = 500,
                Value = 100,
                Font = new Font("Segoe UI", 10)
            };
            panel.Controls.Add(concurrencyNumeric);

            scanButton = CreateStyledButton("🔍 Start Scan", new Point(280, 155), new Size(140, 38));
            scanButton.BackColor = ThemeManager.AccentPrimary;
            scanButton.ForeColor = Color.White;
            scanButton.Font = new Font("Segoe UI", 10, FontStyle.Bold);
            scanButton.Click += async (s, e) => await StartScanAsync();
            panel.Controls.Add(scanButton);

            stopButton = CreateStyledButton("⏹ Stop", new Point(435, 155), new Size(110, 38));
            stopButton.BackColor = ThemeManager.AccentDanger;
            stopButton.ForeColor = Color.White;
            stopButton.Font = new Font("Segoe UI", 10, FontStyle.Bold);
            stopButton.Enabled = false;
            stopButton.Click += (s, e) => StopScan();
            panel.Controls.Add(stopButton);

            exportButton = CreateStyledButton("💾 Export", new Point(560, 155), new Size(110, 38));
            exportButton.BackColor = Color.FromArgb(241, 245, 249);
            exportButton.ForeColor = ThemeManager.TextSecondary;
            exportButton.Font = new Font("Segoe UI", 10, FontStyle.Bold);
            exportButton.Click += ExportResults;
            panel.Controls.Add(exportButton);

            progressBar = new ProgressBar
            {
                Location = new Point(25, 210),
                Size = new Size(panel.Width - 50, 25),
                Style = ProgressBarStyle.Continuous,
                Anchor = AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Top
            };
            panel.Controls.Add(progressBar);

            return panel;
        }

        private Panel CreateStatsPanel()
        {
            var panel = new Panel
            {
                Size = new Size(1040, 120),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            openPortsCard = CreateStatCard("✓", "0", "Open Ports", new Point(20, 15), ThemeManager.AccentSuccess);
            panel.Controls.Add(openPortsCard);

            closedPortsCard = CreateStatCard("✗", "0", "Closed Ports", new Point(220, 15), ThemeManager.AccentDanger);
            panel.Controls.Add(closedPortsCard);

            totalScannedCard = CreateStatCard("📊", "0", "Total Scanned", new Point(420, 15), ThemeManager.AccentSecondary);
            panel.Controls.Add(totalScannedCard);

            scanTimeCard = CreateStatCard("⏱", "0.0s", "Scan Time", new Point(620, 15), ThemeManager.AccentWarning);
            panel.Controls.Add(scanTimeCard);

            statusLabel = new Label
            {
                Text = "Ready to scan",
                Font = new Font("Segoe UI", 9, FontStyle.Italic),
                ForeColor = ThemeManager.TextMuted,
                AutoSize = false,
                Size = new Size(panel.Width - 40, 20),
                Location = new Point(20, 72),
                TextAlign = ContentAlignment.MiddleCenter,
                Anchor = AnchorStyles.Left | AnchorStyles.Right
            };
            panel.Controls.Add(statusLabel);

            return panel;
        }

        private Panel CreateStatCard(string icon, string value, string label, Point location, Color accentColor)
        {
            var card = new Panel
            {
                Location = location,
                Size = new Size(180, 65),
                BackColor = Color.FromArgb(250, 250, 252),
                Padding = new Padding(12)
            };

            var iconLabel = new Label
            {
                Text = icon,
                Font = new Font("Segoe UI", 16, FontStyle.Bold),
                ForeColor = accentColor,
                Location = new Point(12, 10),
                Size = new Size(35, 40),
                TextAlign = ContentAlignment.MiddleCenter
            };
            card.Controls.Add(iconLabel);

            var valueLabel = new Label
            {
                Text = value,
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(50, 8),
                AutoSize = true
            };
            card.Controls.Add(valueLabel);
            card.Tag = valueLabel;

            var labelText = new Label
            {
                Text = label,
                Font = new Font("Segoe UI", 8),
                ForeColor = ThemeManager.TextMuted,
                Location = new Point(52, 35),
                AutoSize = true
            };
            card.Controls.Add(labelText);

            return card;
        }

        private Panel CreateResultsPanel()
        {
            var panel = new Panel
            {
                Size = new Size(1040, 320),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "📋 Scan Results",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 20),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            resultsListView = new ListView
            {
                Location = new Point(20, 60),
                Size = new Size(panel.Width - 40, panel.Height - 80),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Consolas", 9),
                BorderStyle = BorderStyle.FixedSingle,
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom
            };
            
            resultsListView.Columns.Add("Port", 90);
            resultsListView.Columns.Add("Status", 110);
            resultsListView.Columns.Add("Response", 130);
            resultsListView.Columns.Add("Service", 180);
            resultsListView.Columns.Add("Notes", 400);

            panel.Controls.Add(resultsListView);

            return panel;
        }

        private Button CreateStyledButton(string text, Point location, Size size)
        {
            var button = new Button
            {
                Text = text,
                Location = location,
                Size = size,
                FlatStyle = FlatStyle.Flat,
                Cursor = Cursors.Hand
            };
            button.FlatAppearance.BorderSize = 0;
            button.FlatAppearance.MouseOverBackColor = Color.FromArgb(79, 82, 221);
            
            button.Paint += (s, e) =>
            {
                var btn = (Button)s;
                e.Graphics.SmoothingMode = SmoothingMode.AntiAlias;
                using var brush = new SolidBrush(btn.BackColor);
                using var path = GetRoundedRectPath(btn.ClientRectangle, 6);
                e.Graphics.FillPath(brush, path);
                
                var sf = new StringFormat { Alignment = StringAlignment.Center, LineAlignment = StringAlignment.Center };
                e.Graphics.DrawString(btn.Text, btn.Font, new SolidBrush(btn.ForeColor), btn.ClientRectangle, sf);
            };
            
            return button;
        }

        private GraphicsPath GetRoundedRectPath(Rectangle rect, int radius)
        {
            var path = new GraphicsPath();
            int diameter = radius * 2;
            var arc = new Rectangle(rect.Location, new Size(diameter, diameter));

            path.AddArc(arc, 180, 90);
            arc.X = rect.Right - diameter;
            path.AddArc(arc, 270, 90);
            arc.Y = rect.Bottom - diameter;
            path.AddArc(arc, 0, 90);
            arc.X = rect.Left;
            path.AddArc(arc, 90, 90);
            path.CloseFigure();

            return path;
        }

        private async Task StartScanAsync()
        {
            var host = hostTextBox.Text.Trim();
            if (string.IsNullOrWhiteSpace(host))
            {
                MessageBox.Show("Please enter a host or IP address.", "Validation Error", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var ports = ParsePorts(portsTextBox.Text);
            if (ports.Count == 0)
            {
                MessageBox.Show("Please enter valid ports.", "Validation Error", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            resultsListView.Items.Clear();
            progressBar.Value = 0;
            progressBar.Maximum = ports.Count;
            scanButton.Enabled = false;
            stopButton.Enabled = true;
            exportButton.Enabled = false;

            UpdateStatCard(openPortsCard, "0");
            UpdateStatCard(closedPortsCard, "0");
            UpdateStatCard(totalScannedCard, "0");
            UpdateStatCard(scanTimeCard, "0.0s");

            cts = new CancellationTokenSource();
            scanStartTime = DateTime.Now;

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

                var elapsed = (DateTime.Now - scanStartTime).TotalSeconds;
                statusLabel.Text = $"Scan completed in {elapsed:F1}s - {results.Count} ports scanned";
                statusLabel.ForeColor = ThemeManager.AccentSuccess;

                MessageBox.Show($"✓ Scan completed successfully!\n\n" +
                    $"Host: {host}\n" +
                    $"Total ports: {results.Count}\n" +
                    $"Open: {results.Count(r => r.Status == PortStatus.Open)}\n" +
                    $"Closed: {results.Count(r => r.Status == PortStatus.Closed)}\n" +
                    $"Time: {elapsed:F1}s",
                    "Scan Complete", MessageBoxButtons.OK, MessageBoxIcon.Information);
            }
            catch (OperationCanceledException)
            {
                statusLabel.Text = "Scan cancelled by user";
                statusLabel.ForeColor = ThemeManager.AccentWarning;
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                statusLabel.ForeColor = ThemeManager.AccentDanger;
                MessageBox.Show($"Error during scan: {ex.Message}", "Error", 
                    MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                scanButton.Enabled = true;
                stopButton.Enabled = false;
                exportButton.Enabled = resultsListView.Items.Count > 0;
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

            var elapsed = (DateTime.Now - scanStartTime).TotalSeconds;
            UpdateStatCard(scanTimeCard, $"{elapsed:F1}s");

            if (progress.TotalElapsed.HasValue)
            {
                statusLabel.Text = $"Completed {progress.PortsCompleted}/{progress.TotalPorts} ports in {progress.TotalElapsed.Value.TotalSeconds:F1}s";
            }
            else
            {
                statusLabel.Text = $"Scanning... {progress.PortsCompleted}/{progress.TotalPorts} ports ({(progress.PortsCompleted * 100.0 / progress.TotalPorts):F0}%)";
            }

            if (progress.LatestResult != null)
            {
                var item = new ListViewItem(progress.LatestResult.Port.ToString());
                item.SubItems.Add(progress.LatestResult.Status.ToString());
                item.SubItems.Add($"{progress.LatestResult.ResponseTime.TotalMilliseconds:F0} ms");
                
                var service = GetServiceName(progress.LatestResult.Port);
                item.SubItems.Add(service);
                item.SubItems.Add(progress.LatestResult.Notes);

                if (progress.LatestResult.Status == PortStatus.Open)
                {
                    item.BackColor = Color.FromArgb(220, 252, 231);
                    item.ForeColor = Color.FromArgb(22, 101, 52);
                    item.Font = new Font(item.Font, FontStyle.Bold);
                }
                else
                {
                    item.ForeColor = ThemeManager.TextMuted;
                }

                resultsListView.Items.Add(item);
                if (resultsListView.Items.Count > 0)
                {
                    resultsListView.EnsureVisible(resultsListView.Items.Count - 1);
                }
            }

            var openCount = resultsListView.Items.Cast<ListViewItem>().Count(i => i.SubItems[1].Text == "Open");
            var closedCount = resultsListView.Items.Cast<ListViewItem>().Count(i => i.SubItems[1].Text == "Closed");

            UpdateStatCard(openPortsCard, openCount.ToString());
            UpdateStatCard(closedPortsCard, closedCount.ToString());
            UpdateStatCard(totalScannedCard, (openCount + closedCount).ToString());
        }

        private void UpdateStatCard(Panel card, string value)
        {
            if (card.Tag is Label valueLabel)
            {
                valueLabel.Text = value;
            }
        }

        private void StopScan()
        {
            if (cts == null) return;

            stopButton.Enabled = false;
            cts.Cancel();
            statusLabel.Text = "Cancelling scan...";
            statusLabel.ForeColor = ThemeManager.AccentWarning;
        }

        private void ExportResults(object sender, EventArgs e)
        {
            if (resultsListView.Items.Count == 0)
            {
                MessageBox.Show("No results to export.", "Export", MessageBoxButtons.OK, MessageBoxIcon.Information);
                return;
            }

            using var saveDialog = new SaveFileDialog
            {
                Filter = "CSV Files (*.csv)|*.csv|Text Files (*.txt)|*.txt|All Files (*.*)|*.*",
                DefaultExt = "csv",
                FileName = $"PortScan_{hostTextBox.Text}_{DateTime.Now:yyyyMMdd_HHmmss}.csv"
            };

            if (saveDialog.ShowDialog() == DialogResult.OK)
            {
                try
                {
                    var lines = new List<string> { "Port,Status,Response Time,Service,Notes" };
                    foreach (ListViewItem item in resultsListView.Items)
                    {
                        var line = $"{item.Text},{item.SubItems[1].Text},{item.SubItems[2].Text},{item.SubItems[3].Text},\"{item.SubItems[4].Text}\"";
                        lines.Add(line);
                    }
                    System.IO.File.WriteAllLines(saveDialog.FileName, lines);
                    MessageBox.Show($"Results exported successfully to:\n{saveDialog.FileName}", "Export Complete", 
                        MessageBoxButtons.OK, MessageBoxIcon.Information);
                }
                catch (Exception ex)
                {
                    MessageBox.Show($"Error exporting results: {ex.Message}", "Export Error", 
                        MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
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
            20 => "FTP Data Transfer",
            21 => "FTP Control",
            22 => "SSH / Secure Shell",
            23 => "Telnet",
            25 => "SMTP / Email",
            53 => "DNS",
            80 => "HTTP / Web",
            110 => "POP3 / Email",
            135 => "MS RPC",
            139 => "NetBIOS",
            143 => "IMAP / Email",
            443 => "HTTPS / Secure Web",
            445 => "SMB / File Sharing",
            3306 => "MySQL Database",
            3389 => "Remote Desktop (RDP)",
            5432 => "PostgreSQL Database",
            5900 => "VNC Remote Access",
            8080 => "HTTP Proxy / Alt Web",
            8443 => "HTTPS Alternative",
            _ => ""
        };
    }
}
