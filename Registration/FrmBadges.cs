using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using DYMO.Label.Framework;
using Newtonsoft.Json;

namespace Registration
{
    public partial class FrmBadges : Form
    {
        private int SortColumn = 0;
        private bool SortAscend = true;
        private bool _closing = false;

        private List<Badge> Badges { get; set; }
        private readonly System.Threading.Timer _checkTimer;
        public FrmBadges()
        {
            InitializeComponent();

            DoRefresh();

            _checkTimer = new System.Threading.Timer(delegate
                {
                    try
                    {
                        if (_closing) return;
                        var printers = new Printers();
                        if (printers.Any())
                        {
                            if (printers.First().IsConnected)
                            {
                                LblStatus.Invoke(new MethodInvoker(delegate
                                    {
                                        LblStatus.ForeColor = System.Drawing.Color.DarkGreen;
                                        LblStatus.Text = "Connected";
                                    }));
                            }
                            else
                            {
                                LblStatus.Invoke(new MethodInvoker(delegate
                                    {
                                        LblStatus.ForeColor = System.Drawing.Color.Red;
                                        LblStatus.Text = "Disconnected";
                                    }));
                            }
                        }
                        else
                        {
                            LblStatus.Invoke(new MethodInvoker(delegate
                                {
                                    LblStatus.ForeColor = System.Drawing.Color.Red;
                                    LblStatus.Text = "Disconnected";
                                }));
                        }
                    }
                    catch
                    {
                        try
                        {
                            LblStatus.Invoke(new MethodInvoker(delegate
                                {
                                    LblStatus.ForeColor = System.Drawing.Color.Red;
                                    LblStatus.Text = "Error (No Drivers?)";
                                }));
                        }
                        catch
                        {
                        }
                    }
                }, null, 0, 1000);
        }

        private void FrmPrintBadges_FormClosing(object sender, FormClosingEventArgs e)
        {
            _closing = true;
            _checkTimer.Dispose();
        }

        private void DoRefresh()
        {
            Cursor = Cursors.WaitCursor;
            var data = Encoding.ASCII.GetBytes("action=GetBadges"); // &noSilver=true
            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Badges = JsonConvert.DeserializeObject<List<Badge>>(results);

            LstBadges.Items.Clear();
            LstResults.Items.Clear();

            var item = new ListViewItem
            {
                Text = "All Printed Badges"
            };
            item.SubItems.Add(Badges.Count.ToString());
            LstBadges.Items.Add(item);
            var badgeTypes = Badges.Select(badge => badge.Description).Distinct().ToList();
            foreach (var badgeType in badgeTypes)
            {
                item = new ListViewItem
                {
                    Text = badgeType
                };
                item.SubItems.Add(Badges.Count(badge => badge.Description == badgeType).ToString());
                LstBadges.Items.Add(item);
            }
            Cursor = Cursors.Default;
        }

        private void BtnSearch_Click(object sender, EventArgs e)
        {
            BtnPrintSelected.Enabled = false;
            LstResults.BeginUpdate();
            LstResults.Items.Clear();
            if (TxtLastName.Text.Length > 0)
            {
                var results = Badges.FindAll(badge => badge.LastName.ToLower().StartsWith(TxtLastName.Text.ToLower()));
                foreach (var badge in results)
                {
                    var item = new ListViewItem
                        {
                            Text = badge.BadgeNumber.ToString()
                        };
                    item.SubItems.Add(badge.FirstName);
                    item.SubItems.Add(badge.LastName);
                    item.SubItems.Add(badge.BadgeName);
                    item.Tag = badge;
                    if (badge.Status != "Paid")
                        item.BackColor = System.Drawing.Color.LightSalmon;
                    LstResults.Items.Add(item);
                }
            }
            else if (TxtBadgeNumber.Text.Length > 0)
            {
                var badge = Badges.First(b => b.BadgeNumber == Convert.ToInt32(TxtBadgeNumber.Text));
                if (badge == null) return;
                var item = new ListViewItem
                {
                    Text = badge.BadgeNumber.ToString()
                };
                item.SubItems.Add(badge.FirstName);
                item.SubItems.Add(badge.LastName);
                item.SubItems.Add(badge.BadgeName);
                item.Tag = badge;
                if (badge.Status != "Paid")
                    item.BackColor = System.Drawing.Color.LightSalmon;
                LstResults.Items.Add(item);
            }
            else
            {
                foreach (var badge in Badges)
                {
                    var item = new ListViewItem
                    {
                        Text = badge.BadgeNumber.ToString()
                    };
                    item.SubItems.Add(badge.FirstName);
                    item.SubItems.Add(badge.LastName);
                    item.SubItems.Add(badge.BadgeName);
                    item.Tag = badge;
                    if (badge.Status != "Paid")
                        item.BackColor = System.Drawing.Color.LightSalmon;
                    LstResults.Items.Add(item);
                }
            }
            LstResults.ListViewItemSorter = new ListViewItemComparer(SortColumn, SortAscend);
            LstResults.Sort();
            LstResults.EndUpdate();
        }

        private void LstResults_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(SortColumn) == Math.Abs(e.Column))
            {
                SortAscend = !SortAscend;
                LstResults.Columns[e.Column].ImageIndex = SortAscend ? 0 : 1;
            }
            else
            {
                LstResults.Columns[SortColumn].ImageIndex = -1;
                LstResults.Columns[SortColumn].TextAlign = LstResults.Columns[SortColumn].TextAlign;
                SortAscend = true;
                SortColumn = e.Column;
                LstResults.Columns[e.Column].ImageIndex = 0;
            }

            LstResults.BeginUpdate();
            LstResults.ListViewItemSorter = new ListViewItemComparer(e.Column, SortAscend);
            LstResults.Sort();
            LstResults.EndUpdate();
        }

        private void LstBadges_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnPrintAll.Enabled = LstBadges.SelectedItems.Count > 0;
            var enableBackOnly = false;
            if(LstBadges.SelectedItems.Count > 0)
            {
                var badgeType = LstBadges.SelectedItems[0].Text;
                enableBackOnly = (badgeType == "Concom" || badgeType == "Guest of Honor");
            }
            chkBackOnly.Enabled = enableBackOnly;
            chkBackOnly.Checked = enableBackOnly;
        }

        private void LstResults_SelectedIndexChanged(object sender, EventArgs e)
        {
            BtnPrintSelected.Enabled = LstResults.SelectedItems.Count > 0;
            BtnEditBadge.Enabled = LstResults.SelectedItems.Count > 0;
        }

        private void BtnPrintSelected_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in LstResults.SelectedItems)
            {
                var badge = (Badge) item.Tag;
                if(badge.Status != "Paid")
                {
                    MessageBox.Show("The badge for " + badge.Name + " (" + badge.BadgeName + ") has a status of '" + badge.Status + 
                        "' and cannot be printed.", "Unable to Print Badge", MessageBoxButtons.OK,
                        MessageBoxIcon.Exclamation);
                }
                else
                    Badge.PrintBadge(badge, chkBackOnly.Checked);
            }
        }

        private void BtnPrintAll_Click(object sender, EventArgs e)
        {
            if (LstBadges.SelectedItems.Count == 0) return;

            var badgeType = LstBadges.SelectedItems[0].Text;
            var startAt = TxtResumeNum.Text.Length > 0 ? Convert.ToInt32(TxtResumeNum.Text) : 0;
            var badges = Badges.FindAll(b => b.Description == badgeType).Where(b => b.Status == "Paid").ToList();
            if (rdoOrderNumber.Checked)
                badges = badges.OrderBy(b => b.BadgeNumber).ToList();
            else
                badges = badges.OrderBy(b => b.LastName.ToUpper()).ThenBy(b => b.FirstName.ToUpper()).ToList();

            var dialog = new FrmPrinting(badges.Count);
            dialog.Show();

            int count = 0;
            foreach (var badge in badges)
            {
                count++;
                if (badge.BadgeNumber < startAt) continue;
                Application.DoEvents();
                if (!dialog.SetDisplay(count, badge.BadgeNumber + ": " + badge.BadgeName))
                {
                    TxtResumeNum.Text = badge.BadgeNumber.ToString();
                    count = 0;
                    break;
                }
                Badge.PrintBadge(badge, chkBackOnly.Checked);
            }
            dialog.Close();
            if (count == badges.Count) TxtResumeNum.Text = "";
        }

        private void LstResults_DoubleClick(object sender, EventArgs e)
        {
            EditBadge();
        }

        private void BtnEditBadge_Click(object sender, EventArgs e)
        {
            EditBadge();
        }

        private void EditBadge()
        {
            if (LstResults.SelectedItems.Count == 0) return;
            var badge = (Badge)LstResults.SelectedItems[0].Tag;
            var editBadge = new FrmEditBadge(badge);
            editBadge.ShowDialog();
            editBadge.Close();

            if (editBadge.Badge != null)
            {
                Badges[Badges.IndexOf(badge)] = editBadge.Badge;
                LstResults.SelectedItems[0].SubItems[1].Text = editBadge.Badge.FirstName;
                LstResults.SelectedItems[0].SubItems[2].Text = editBadge.Badge.LastName;
                LstResults.SelectedItems[0].SubItems[3].Text = editBadge.Badge.BadgeName;
                LstResults.SelectedItems[0].Tag = editBadge.Badge;
                if (editBadge.Badge.Status == "Paid")
                    LstResults.SelectedItems[0].BackColor = System.Drawing.Color.White;
                else
                    LstResults.SelectedItems[0].BackColor = System.Drawing.Color.LightSalmon;
            }
            else
            {
                Badges.Remove(badge);
                LstResults.Items.Remove(LstResults.SelectedItems[0]);
            }
        }

        private void BtnCompBadge_Click(object sender, EventArgs e)
        {
            var comp = new FrmCompBadge();
            comp.ShowDialog();
            comp.Close();
        }

        private void BtnRefresh_Click(object sender, EventArgs e)
        {
            Cursor = Cursors.WaitCursor;
            DoRefresh();
            Cursor = Cursors.Default;
        }

        private void TxtLastName_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                BtnSearch_Click(this, new EventArgs());
                e.Handled = true;
            }
        }

        private void TxtBadgeNumber_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                BtnSearch_Click(this, new EventArgs());
                e.Handled = true;
            }
        }
    }
}
