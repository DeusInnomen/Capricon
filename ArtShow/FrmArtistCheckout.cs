﻿using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Windows.Forms;
using Newtonsoft.Json;

namespace ArtShow
{
    public partial class FrmArtistCheckout : Form
    {
        private List<CheckoutItems> Items { get; set; }
        private ArtistPresence Presence { get; set; }
        public FrmArtistCheckout(ArtistPresence presence)
        {
            InitializeComponent();
            Presence = presence;
            CmbMarkMode.SelectedIndex = 0;

            var data = Encoding.ASCII.GetBytes("action=GetArtistCheckout&id=" + Presence.ArtistAttendingID + "&Year=" + Program.Year.ToString());
            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            Items = JsonConvert.DeserializeObject<List<CheckoutItems>>(results);

            decimal showTotal = 0;
            decimal shopTotal = 0;

            foreach (var piece in Items.FindAll(i => !i.IsPrintShop))
            {
                var item = new ListViewItem()
                {
                    Text = piece.ShowNumber.ToString()
                };
                item.SubItems.Add(piece.LocationCode);
                item.SubItems.Add(piece.Title);
                item.SubItems.Add(piece.Media);
                if (piece.FinalSalePrice != null)
                {
                    item.SubItems.Add(((decimal) piece.FinalSalePrice).ToString("C"));
                    showTotal += (decimal)piece.FinalSalePrice;
                }
                else
                    item.SubItems.Add("Not Sold");
                item.SubItems.Add(piece.Claimed ? "Yes" : "No");
                item.Tag = piece;
                LstShowItems.Items.Add(item);
            }

            foreach (var piece in Items.FindAll(i => i.IsPrintShop))
            {
                var item = new ListViewItem()
                {
                    Text = piece.ShowNumber.ToString()
                };
                item.SubItems.Add(piece.LocationCode);
                item.SubItems.Add(piece.Title);
                item.SubItems.Add(piece.Media);
                item.SubItems.Add((piece.QuantitySent - piece.QuantitySold).ToString());
                item.SubItems.Add(((decimal)(piece.QuantitySold * piece.QuickSalePrice)).ToString("C"));
                item.SubItems.Add(piece.Claimed ? "Yes" : "No");
                item.Tag = piece;
                LstShopItems.Items.Add(item);
                shopTotal += (decimal)piece.QuantitySold * (decimal)piece.QuickSalePrice;
            }

            LblShowTotal.Text = showTotal.ToString("C");
            LblShopTotal.Text = shopTotal.ToString("C");
            var conShare = (showTotal + shopTotal)*(decimal) 0.1;
            LblConShare.Text = conShare.ToString("C");
            LblTotalOwed.Text = (showTotal + shopTotal - conShare).ToString("C");
        }

        private void FrmArtistCheckout_Load(object sender, EventArgs e)
        {

        }

        private void BtnMarkClaimed_Click(object sender, EventArgs e)
        {
            var mode = CmbMarkMode.SelectedIndex;
            var data = Encoding.ASCII.GetBytes("action=MarkArtistPickup&id=" + Presence.ArtistAttendingID + "&mode=" + mode);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var resultsJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var results = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(resultsJson);

            foreach (ListViewItem item in LstShowItems.Items)
            {
                var shopItem = (CheckoutItems) item.Tag;
                if (mode == 1 || (mode == 0 && shopItem.PurchaserID == null))
                {
                    shopItem.Claimed = true;
                    item.SubItems[5].Text = "Yes";
                }
            }
            foreach (ListViewItem item in LstShopItems.Items)
            {
                var shopItem = (CheckoutItems)item.Tag;
                shopItem.Claimed = true;
                item.SubItems[6].Text = "Yes";
            }
        }

        private void BtnPrintCheckout_Click(object sender, EventArgs e)
        {
            var dialog = new FrmArtistCheckoutSheet(Items);
            dialog.ShowDialog();
            dialog.Close();
            DialogResult = DialogResult.None;
        }
    }
}
