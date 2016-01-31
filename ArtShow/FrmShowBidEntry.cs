using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmShowBidEntry : Form
    {
        public List<ArtShowItem> Items { get; set; }
        private ArtShowItem Current { get; set; }
        private int CurrentIndex { get; set; }

        public FrmShowBidEntry(List<ArtShowItem> items )
        {
            InitializeComponent();
            Items = items;
            Current = null;
            CurrentIndex = -1;

            LstItems.BeginUpdate();
            foreach (var showItem in items)
            {
                var item = new ListViewItem {Text = showItem.ShowNumber.ToString()};
                item.SubItems.Add(showItem.Title);
                item.SubItems.Add(showItem.PurchaserNumber.ToString());
                item.SubItems.Add(showItem.FinalSalePrice != null ? ((decimal)showItem.FinalSalePrice).ToString("C") : "");
                item.BackColor = showItem.Auctioned ? Color.LightGreen : Color.White;
                item.Tag = showItem;
                LstItems.Items.Add(item);
            }
            LstItems.ListViewItemSorter = new ListViewItemComparer(0, true);
            LstItems.Sort();
            LstItems.EndUpdate();
            LstItems.Items[0].Selected = true;
        }

        private void TxtFinalBid_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == 13)
            {
                if (LstItems.SelectedItems.Count > 0)
                {
                    var index = LstItems.SelectedItems[0].Index + 1;
                    if (index == LstItems.Items.Count) index = 0;
                    LstItems.Items[index].Selected = true;
                }
                else
                    LstItems.Items[0].Selected = true;
                e.Handled = true;
                TxtBuyerNum.Focus();
            }
        }

        private void LstItems_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (LstItems.SelectedItems.Count == 0) return;
            if (Current != null && Current.ArtID == ((ArtShowItem) LstItems.SelectedItems[0].Tag).ArtID) return;
            if (Current != null)
            {
                int? buyer = TxtBuyerNum.Text.Trim().Length > 0 ? Convert.ToInt32(TxtBuyerNum.Text) : (int?) null;
                decimal? price = TxtFinalBid.Text.Trim().Length > 0 ? Convert.ToDecimal(TxtFinalBid.Text) : (decimal?)null;
                if (buyer != Current.PurchaserNumber || price != Current.FinalSalePrice || ChkAuction.Checked != Current.Auctioned)
                {
                    Current.PurchaserNumber = buyer;
                    Current.FinalSalePrice = price;
                    Current.Auctioned = ChkAuction.Checked;
                    if (buyer != null && price != null)
                        Current.Category = "Sold";
                    else if (ChkAuction.Checked)
                        Current.Category = "Live Auction";
                    else if (string.IsNullOrEmpty(Current.Category))
                        Current.Category = "Not Sold";
                    Current.Save();
                    LstItems.Items[CurrentIndex].SubItems[2].Text = buyer != null
                                                                        ? ((int)buyer).ToString()
                                                                        : "";
                    LstItems.Items[CurrentIndex].SubItems[3].Text = price != null
                                                                        ? ((decimal)price).ToString(
                                                                            "C")
                                                                        : "";
                    LstItems.Items[CurrentIndex].BackColor = Current.Auctioned ? Color.LightGreen : Color.White;
                }
            }
            Current = (ArtShowItem) LstItems.SelectedItems[0].Tag;
            CurrentIndex = LstItems.SelectedIndices[0];
            if (Current.ShowNumber != null) TxtNumber.Text = ((int)Current.ShowNumber).ToString();
            TxtTitle.Text = Current.Title;
            TxtBuyerNum.Text = Current.PurchaserNumber != null ? ((int) Current.PurchaserNumber).ToString() : "";
            TxtFinalBid.Text = Current.FinalSalePrice != null ? ((decimal)Current.FinalSalePrice).ToString() : "";
            ChkAuction.Checked = Current.Auctioned;
        }

        private void BtnClose_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }

        private void FrmShowBidEntry_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (Current != null)
            {
                int? buyer = TxtBuyerNum.Text.Trim().Length > 0 ? Convert.ToInt32(TxtBuyerNum.Text) : (int?)null;
                decimal? price = TxtFinalBid.Text.Trim().Length > 0 ? Convert.ToDecimal(TxtFinalBid.Text) : (decimal?)null;
                if (buyer != Current.PurchaserNumber || price != Current.FinalSalePrice || ChkAuction.Checked != Current.Auctioned)
                {
                    Current.PurchaserNumber = buyer;
                    Current.FinalSalePrice = price;
                    Current.Auctioned = ChkAuction.Checked;
                    if (buyer != null && price != null)
                        Current.Category = "Sold";
                    else if (ChkAuction.Checked)
                        Current.Category = "Live Auction";
                    else if (string.IsNullOrEmpty(Current.Category))
                        Current.Category = "Not Sold";
                    Current.Save();
                }
            }
        }

        private void FrmShowBidEntry_Load(object sender, EventArgs e)
        {
            TxtBuyerNum.Focus();
        }

        private void BtnMoveNext_Click(object sender, EventArgs e)
        {
            if (LstItems.SelectedItems.Count > 0)
            {
                var index = LstItems.SelectedItems[0].Index + 1;
                if (index == LstItems.Items.Count) index = 0;
                LstItems.Items[index].Selected = true;
            }
            else
                LstItems.Items[0].Selected = true;
            TxtBuyerNum.Focus();
        }
    }
}
