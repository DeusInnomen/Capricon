using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Drawing.Imaging;
using System.Drawing.Printing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Windows.Forms;
using Newtonsoft.Json;

namespace ArtShow
{
    public partial class FrmArtistInventory : Form
    {
        private Artist Artist { get; set; }
        private ArtistPresence Presence { get; set; }
        private List<ArtShowItem> ShowItems { get; set; }
        private List<PrintShopItem> ShopItems { get; set; } 

        private List<ArtShowItem> _AuctionTagsToPrint = new List<ArtShowItem>();
        private List<PrintShopItem> _PrintShopTagsToPrint = new List<PrintShopItem>();
        private int _currentTagPrintNumber;

        private int ArtShowSortColumn = 0;
        private bool ArtShowSortAscend = true;
        private int PrintShopSortColumn = 0;
        private bool PrintShopSortAscend = true;

        public FrmArtistInventory(Artist artist, ArtistPresence presence)
        {
            InitializeComponent();
            Artist = artist;
            Text = Artist.DisplayName + " -- Art Inventory";
            Presence = presence;

            var data = Encoding.ASCII.GetBytes("action=GetInventory&AttendID=" + Presence.ArtistAttendingID);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            if (artist.IsEAP || artist.IsCharity)
                BtnHangingFees.Enabled = false;

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var inventory = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);

            if (inventory["showPieces"] != null)
            {
                ShowItems = JsonConvert.DeserializeObject<List<ArtShowItem>>(inventory["showPieces"].ToString());
                foreach (var piece in ShowItems)
                {
                    var item = new ListViewItem()
                        {
                            Text = piece.ShowNumber.ToString()
                        };
                    item.SubItems.Add(piece.Title);
                    item.SubItems.Add(piece.Media);
                    var num = piece.PrintNumber ?? "";
                    if (piece.PrintMaxNumber != null) num += " of " + piece.PrintMaxNumber.ToString();
                    item.SubItems.Add(num);
                    var bid = piece.MinimumBid != null ? Convert.ToSingle(piece.MinimumBid).ToString("C") : "Not For Sale";
                    item.SubItems.Add(bid);
                    item.SubItems.Add(piece.LocationCode);
                    item.SubItems.Add(piece.Category);
                    item.SubItems.Add(piece.CheckedIn ? "Yes" : "No");
                    item.Tag = piece;
                    if (piece.FeesPaid || Artist.IsEAP || Artist.IsCharity) item.BackColor = Color.LightGreen;
                    lstArtShow.Items.Add(item);
                }

            }

            if (inventory["printShopPieces"] != null)
            {
                ShopItems = JsonConvert.DeserializeObject<List<PrintShopItem>>(inventory["printShopPieces"].ToString());
                foreach (var piece in ShopItems)
                {
                    var item = new ListViewItem()
                    {
                        Text = piece.ShowNumber.ToString()
                    };
                    item.SubItems.Add(piece.Title);
                    item.SubItems.Add(piece.Media);
                    item.SubItems.Add(piece.QuantitySent.ToString());
                    item.SubItems.Add(Convert.ToSingle(piece.Price).ToString("C"));
                    item.SubItems.Add(piece.LocationCode);
                    item.SubItems.Add(piece.Category);
                    item.SubItems.Add(piece.CheckedIn ? "Yes" : "No");
                    item.Tag = piece;
                    lstPrintShop.Items.Add(item);
                }

            }
        }

        public override sealed string Text
        {
            get { return base.Text; }
            set { base.Text = value; }
        }

        private void BtnClose_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }

        private void BtnEditItemArtShow_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in lstArtShow.SelectedItems)
            {
                var showItem = (ArtShowItem) item.Tag;
                var dialog = new FrmEditShowItem(showItem);
                if (dialog.ShowDialog() != DialogResult.OK) continue;
                item.Tag = dialog.ShowItem;
                item.SubItems[1].Text = dialog.ShowItem.Title;
                item.SubItems[2].Text = dialog.ShowItem.Media;
                var num = dialog.ShowItem.PrintNumber ?? "";
                if (dialog.ShowItem.PrintMaxNumber != null)
                    num += " of " + dialog.ShowItem.PrintMaxNumber.ToString();
                item.SubItems[3].Text = num;
                var bid = dialog.ShowItem.MinimumBid != null
                              ? Convert.ToSingle(dialog.ShowItem.MinimumBid).ToString("C")
                              : "Not For Sale";
                item.SubItems[4].Text = bid;
                item.SubItems[5].Text = dialog.ShowItem.LocationCode;
                item.SubItems[6].Text = dialog.ShowItem.Category;
            }
        }

        private void BtnAddToArtShow_Click(object sender, EventArgs e)
        {
            var dialog = new FrmEditShowItem(null, (int)Presence.ArtistAttendingID);
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                var item = new ListViewItem
                    {
                    Text = dialog.ShowItem.ShowNumber.ToString()
                };
                item.SubItems.Add(dialog.ShowItem.Title);
                item.SubItems.Add(dialog.ShowItem.Media);
                var num = dialog.ShowItem.PrintNumber ?? "";
                if (dialog.ShowItem.PrintMaxNumber != null) num += " of " + dialog.ShowItem.PrintMaxNumber;
                item.SubItems.Add(num);
                var bid = dialog.ShowItem.MinimumBid != null ? Convert.ToSingle(dialog.ShowItem.MinimumBid).ToString("C") : "Not For Sale";
                item.SubItems.Add(bid);
                item.SubItems.Add(dialog.ShowItem.LocationCode);
                item.SubItems.Add(dialog.ShowItem.Category);
                item.SubItems.Add("No");
                item.Tag = dialog.ShowItem;
                lstArtShow.Items.Add(item);
            }
        }

        private void BtnCheckInArtShow_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in lstArtShow.SelectedItems)
            {
                var showItem = (ArtShowItem)item.Tag;
                showItem.CheckedIn = !showItem.CheckedIn;
                showItem.Save();
                item.SubItems[7].Text = showItem.CheckedIn ? "Yes" : "No";
                item.Tag = showItem;
            }
        }

        private void BtnEditItemShop_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in lstPrintShop.SelectedItems)
            {
                var shopItem = (PrintShopItem)item.Tag;
                var dialog = new FrmEditShopItem(shopItem);
                if (dialog.ShowDialog() != DialogResult.OK) continue;
                item.Tag = dialog.ShopItem;
                item.SubItems[1].Text = dialog.ShopItem.Title;
                item.SubItems[2].Text = dialog.ShopItem.Media;
                item.SubItems[3].Text = dialog.ShopItem.QuantitySent.ToString();
                item.SubItems[4].Text = dialog.ShopItem.Price.ToString("C");
                item.SubItems[5].Text = dialog.ShopItem.LocationCode;
                item.SubItems[6].Text = dialog.ShopItem.Category;
            }
        }

        private void BtnAddItemShop_Click(object sender, EventArgs e)
        {
            var dialog = new FrmEditShopItem(null, (int)Presence.ArtistAttendingID);
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                var item = new ListViewItem()
                {
                    Text = dialog.ShopItem.ShowNumber.ToString()
                };
                item.SubItems.Add(dialog.ShopItem.Title);
                item.SubItems.Add(dialog.ShopItem.Media);
                item.SubItems.Add(dialog.ShopItem.QuantitySent.ToString());
                item.SubItems.Add(Convert.ToSingle(dialog.ShopItem.Price).ToString("C"));
                item.SubItems.Add(dialog.ShopItem.LocationCode);
                item.SubItems.Add(dialog.ShopItem.Category);
                item.SubItems.Add("No");
                item.Tag = dialog.ShopItem;
                lstPrintShop.Items.Add(item);
            }
        }

        private void BtnCheckInShop_Click(object sender, EventArgs e)
        {
            foreach (ListViewItem item in lstPrintShop.SelectedItems)
            {
                var shopItem = (PrintShopItem)item.Tag;
                shopItem.CheckedIn = !shopItem.CheckedIn;
                shopItem.Save();
                item.SubItems[7].Text = shopItem.CheckedIn ? "Yes" : "No";
                item.Tag = shopItem;
            }
        }

        private void BtnHangingFees_Click(object sender, EventArgs e)
        {
            decimal feesDue = 0;
            foreach (ListViewItem item in lstArtShow.Items)
            {
                var showItem = (ArtShowItem) item.Tag;
                if (showItem.FeesPaid || Artist.IsEAP || Artist.IsCharity) continue;
                if (showItem.MinimumBid != null && showItem.MinimumBid < 100)
                    feesDue += (decimal) 0.5;
                else
                    feesDue += 1;
            }
            if(feesDue > 0)
            {
            var fees = new FrmHangingFees(Presence, Artist, feesDue);
            if(fees.ShowDialog() == DialogResult.OK)
                foreach (ListViewItem item in lstArtShow.Items)
                {
                    var showItem = (ArtShowItem)item.Tag;
                    if (showItem.FeesPaid) continue;
                    item.BackColor = Color.LightGreen;
                    showItem.FeesPaid = true;
                    showItem.Save();
                }
            }
        }

        private void BtnArtShowControl_Click(object sender, EventArgs e)
        {
            var items = (from ListViewItem item in lstArtShow.Items select (ArtShowItem)item.Tag).ToList();
            var report = new FrmArtShowControl(items);
            report.ShowDialog();
        }

        private void BtnPrintShopControl_Click(object sender, EventArgs e)
        {
            var items = (from ListViewItem item in lstPrintShop.Items select (PrintShopItem)item.Tag).ToList();
            var report = new FrmPrintShopControl(Artist, items);
            report.ShowDialog();
        }

        private void BtnPrintTags_Click(object sender, EventArgs e)
        {
            if (lstArtShow.SelectedItems.Count == 0) return;
            var document = new PrintDocument();
            document.PrintPage += AuctionDocumentOnPrintPage;
            var dialog = new PrintDialog
                {
                    Document = document,
                    AllowSomePages = true,
                    AllowCurrentPage = false,
                    AllowSelection = true,
                    AllowPrintToFile = true
                };
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                _AuctionTagsToPrint.AddRange(
                    (from ListViewItem item in lstArtShow.SelectedItems select (ArtShowItem) item.Tag).ToList());
                document.Print();
            }
        }

        private void BtnPrintAllTags_Click(object sender, EventArgs e)
        {
            var document = new PrintDocument();
            document.PrintPage += AuctionDocumentOnPrintPage;
            var dialog = new PrintDialog
            {
                Document = document,
                AllowSomePages = true,
                AllowCurrentPage = false,
                AllowSelection = true,
                AllowPrintToFile = true
            };
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                _AuctionTagsToPrint.AddRange(
                    (from ListViewItem item in lstArtShow.Items select (ArtShowItem)item.Tag).ToList());
                document.Print();
            }
        }

        private void AuctionDocumentOnPrintPage(object sender, PrintPageEventArgs e)
        {
            var widthInch = e.PageBounds.Width / 8.5;
            var heightInch = e.PageBounds.Height / 11;
            var leftMargin = widthInch * 0.25;
            var topMargin = heightInch * 0.2;
            var tagNumber = 1;
            while (tagNumber <= 4)
            {
                var tag = CompressTag(DrawTag(_AuctionTagsToPrint[0], e));
                e.Graphics.DrawImage(tag,
                    (float)(tagNumber % 2 == 0 ? ((widthInch * 4.25) + leftMargin) : leftMargin),
                    (float)(tagNumber < 3 ? topMargin : heightInch * 5.5 + topMargin),
                    (float)(widthInch * 3.25),
                    (float)(heightInch * 5));
                _AuctionTagsToPrint.RemoveAt(0);
                if (_AuctionTagsToPrint.Count == 0) break;
                tagNumber++;
            }
            if (_AuctionTagsToPrint.Count > 0) e.HasMorePages = true;
        }

        private Image CompressTag(Bitmap tag)
        {
            var ms = new MemoryStream();
            tag.Save(ms, ImageFormat.Png);
            var output = Image.FromStream(ms);
            return output;
        }

        private Bitmap DrawTag(ArtShowItem item, PrintPageEventArgs e)
        {
            var year = (Program.Year - 1980).ToString();
            var fontHeader = new Font("Lucida Sans", 18, FontStyle.Bold);
            var fontText = new Font("Lucida Sans", 14);
            var fontTable = new Font("Lucida Sans", 12);
            var fontFootnote = new Font("Lucida Sans", 12);
            var fontFootnoteBold = new Font("Lucida Sans", 12, FontStyle.Bold);
            var centered = new StringFormat { LineAlignment = StringAlignment.Center, Alignment = StringAlignment.Center };
            var leftButCentered = new StringFormat { LineAlignment = StringAlignment.Center, Alignment = StringAlignment.Near };

            var settings = new PrinterSettings();
            var resX = settings.DefaultPageSettings.PrinterResolution.X;
            var resY = settings.DefaultPageSettings.PrinterResolution.Y;
            var imageWidth = Convert.ToInt32(((float)e.PageBounds.Width / 200) * resX);
            var imageHeight = Convert.ToInt32((((float)e.PageBounds.Height + 375) / 200) * resY);

            var image = new Bitmap(imageWidth, imageHeight);
            image.SetResolution(resX, resY);
            var gfx = Graphics.FromImage(image);            
            var currentY = 0F;

            var text = "Capricon " + year + " Art Show";
            gfx.DrawString(text, fontHeader, Brushes.Black, new RectangleF(0, currentY, image.Width, fontHeader.GetHeight(gfx)), centered);
            currentY += (float)(fontHeader.GetHeight(gfx) * 1.5);

            text = "Artist #: " + Artist.ArtistNumber;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            text = "Piece #: " + item.ShowNumber;
            var textWidth = gfx.MeasureString(text, fontText).Width;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(image.Width - textWidth, currentY, textWidth, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Artist: " + Artist.DisplayName;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            text = Artist.IsPro ? "Pro" : "Fan";
            textWidth = gfx.MeasureString(text, fontText).Width;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(image.Width - textWidth, currentY, textWidth, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Title: " + item.Title;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Medium: " + item.Media;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Print: " + (item.IsOriginal ? "No" : "Yes") + "     Number: ";
            if (item.PrintNumber != null)
            {
                text += item.PrintNumber;
                if (item.PrintMaxNumber != null) text += " of " + item.PrintMaxNumber;
            }
            else
                text += "N/A";
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Publishing Rights: Yes / No / Ask";
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            text = "Minimum Bid: " + (item.MinimumBid != null ? Convert.ToDecimal(item.MinimumBid).ToString("C") : "Not For Sale");
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(fontText.GetHeight(gfx) * 1.5);

            var rowHeight = fontTable.GetHeight(gfx) + 2;
            var row1 = (float)(image.Width * 0.08);
            var row2 = (float)(image.Width * 0.59);
            var row3 = (float)(image.Width * 0.79);

            var thickLine = new Pen(Color.Black, 6);

            gfx.DrawLine(thickLine, 0, currentY, image.Width - 1, currentY);
            gfx.DrawLine(thickLine, 0, currentY, 0, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, row1, currentY, row1, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, row2, currentY, row2, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, row3, currentY, row3, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, image.Width - 1, currentY, image.Width - 1, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, 0, currentY + (rowHeight * 2), image.Width - 1, currentY + (rowHeight * 2));
            gfx.DrawString("Name", fontTable, Brushes.Black, new RectangleF(row1 + 2, currentY + 1, row2 - row1, (rowHeight * 2) - 1), leftButCentered);
            gfx.DrawString("Badge #", fontTable, Brushes.Black, new RectangleF(row2, currentY + 1, row3 - row2, (rowHeight * 2) - 1), centered);
            gfx.DrawString("Bid Amount", fontTable, Brushes.Black, new RectangleF(row3, currentY + 1, image.Width - row3, (rowHeight * 2) - 1), centered);
            currentY += (rowHeight * 2) + 1;

            for (int rowNumber = 1; rowNumber <= 5; rowNumber++)
            {
                gfx.DrawLine(thickLine, 0, currentY, image.Width - 1, currentY);
                gfx.DrawLine(thickLine, 0, currentY, 0, (float) (currentY + rowHeight*2));
                gfx.DrawLine(thickLine, row1, currentY, row1, (float)(currentY + rowHeight * 2));
                gfx.DrawLine(thickLine, row2, currentY, row2, (float)(currentY + rowHeight * 2));
                gfx.DrawLine(thickLine, row3, currentY, row3, (float)(currentY + rowHeight * 2));
                gfx.DrawLine(thickLine, image.Width - 1, currentY, image.Width - 1, (float)(currentY + rowHeight * 2));
                gfx.DrawLine(thickLine, 0, (float) (currentY + rowHeight*2), image.Width - 1, (float)(currentY + rowHeight * 2));
                gfx.DrawString(rowNumber.ToString(), fontTable, Brushes.Black, new RectangleF(1, currentY + 1, row1 - 1, rowHeight * 2 - 1), centered);
                currentY += (float)(rowHeight * 2) + 1;
            }

            gfx.DrawLine(thickLine, 0, currentY, image.Width - 1, currentY);
            gfx.DrawLine(thickLine, 0, currentY, 0, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, row2, currentY, row2, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, row3, currentY, row3, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, image.Width - 1, currentY, image.Width - 1, currentY + rowHeight * 2);
            gfx.DrawLine(thickLine, 0, currentY + rowHeight * 2, image.Width - 1, currentY + rowHeight * 2);
            gfx.DrawString("Auction", fontTable, Brushes.Black, new RectangleF(1, currentY + 1, row2 - 1, rowHeight * 2 - 1), centered);
            currentY += (float)(rowHeight * 2) + 1;

            text = "Do not ";
            gfx.DrawString(text, fontFootnoteBold, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            var text2 = "cross off bids! Ask for assistance.";
            gfx.DrawString(text2, fontFootnote, Brushes.Black,
                new RectangleF(gfx.MeasureString(text, fontFootnoteBold).Width, currentY, image.Width, fontText.GetHeight(gfx)));
            currentY += (float)(rowHeight * 1.5);

            text = "Final Total: $";
            gfx.DrawString(text, fontTable, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)));
            text = "    Badge #:";
            gfx.DrawString(text, fontTable, Brushes.Black, new RectangleF((float)(image.Width / 2), currentY, (float)(image.Width / 2), fontText.GetHeight(gfx)));

            gfx.Dispose();

            var resized = new Bitmap(image, image.Width / 4, image.Height / 4);
            return resized;
        }

        private void BtnDeleteShowItem_Click(object sender, EventArgs e)
        {
            if (MessageBox.Show("Are you sure you wish to delete the selected item(s)?", "Confirmation",
                                MessageBoxButtons.YesNo,
                                MessageBoxIcon.Question, MessageBoxDefaultButton.Button2) == DialogResult.No) return;
            var tags = "";
            foreach (ListViewItem item in lstArtShow.SelectedItems)
                tags += ", " + ((ArtShowItem) item.Tag).ArtID;

            var data = Encoding.ASCII.GetBytes("action=DeleteArtItems&ids=" + tags.Substring(2));

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var inventory = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);

            foreach (ListViewItem item in lstArtShow.SelectedItems)
                lstArtShow.Items.Remove(item);

        }

        private void BtnDeleteShopItem_Click(object sender, EventArgs e)
        {
            if (MessageBox.Show("Are you sure you wish to delete the selected item(s)?", "Confirmation",
                                MessageBoxButtons.YesNo,
                                MessageBoxIcon.Question, MessageBoxDefaultButton.Button2) == DialogResult.No) return;
            var tags = "";
            foreach (ListViewItem item in lstPrintShop.SelectedItems)
                tags += ", " + ((ArtShowItem)item.Tag).ArtID;

            var data = Encoding.ASCII.GetBytes("action=DeleteArtItems&ids=" + tags.Substring(2));

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(response.GetResponseStream()).ReadToEnd();
            var inventory = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);

            foreach (ListViewItem item in lstPrintShop.SelectedItems)
                lstArtShow.Items.Remove(item);
        }

        private void BtnShowBids_Click(object sender, EventArgs e)
        {
            var dialog = new FrmShowBidEntry(ShowItems);
            dialog.ShowDialog();
            dialog.Close();
        }

        private void BtnCheckout_Click(object sender, EventArgs e)
        {
            var dialog = new FrmArtistCheckout(Presence);
            dialog.ShowDialog();
            dialog.Close();
        }

        private void lstArtShow_DoubleClick(object sender, EventArgs e)
        {
            if(lstArtShow.SelectedItems.Count > 0)
            {
                var item = lstArtShow.SelectedItems[0];
                var showItem = (ArtShowItem)item.Tag;
                var dialog = new FrmEditShowItem(showItem);
                if (dialog.ShowDialog() != DialogResult.OK) return;
                item.Tag = dialog.ShowItem;
                item.SubItems[1].Text = dialog.ShowItem.Title;
                item.SubItems[2].Text = dialog.ShowItem.Media;
                var num = dialog.ShowItem.PrintNumber ?? "";
                if (dialog.ShowItem.PrintMaxNumber != null)
                    num += " of " + dialog.ShowItem.PrintMaxNumber.ToString();
                item.SubItems[3].Text = num;
                var bid = dialog.ShowItem.MinimumBid != null
                              ? Convert.ToSingle(dialog.ShowItem.MinimumBid).ToString("C")
                              : "Not For Sale";
                item.SubItems[4].Text = bid;
                item.SubItems[5].Text = dialog.ShowItem.LocationCode;
                item.SubItems[6].Text = dialog.ShowItem.Category;
            }

        }

        private void lstPrintShop_DoubleClick(object sender, EventArgs e)
        {
            if(lstPrintShop.SelectedItems.Count > 0)
            {
                var item = lstPrintShop.SelectedItems[0];
                var shopItem = (PrintShopItem)item.Tag;
                var dialog = new FrmEditShopItem(shopItem);
                if (dialog.ShowDialog() != DialogResult.OK) return;
                item.Tag = dialog.ShopItem;
                item.SubItems[1].Text = dialog.ShopItem.Title;
                item.SubItems[2].Text = dialog.ShopItem.Media;
                item.SubItems[3].Text = dialog.ShopItem.QuantitySent.ToString();
                item.SubItems[4].Text = dialog.ShopItem.Price.ToString("C");
                item.SubItems[5].Text = dialog.ShopItem.LocationCode;
                item.SubItems[6].Text = dialog.ShopItem.Category;
            }
        }

        private void lstArtShow_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(ArtShowSortColumn) == Math.Abs(e.Column))
            {
                ArtShowSortAscend = !ArtShowSortAscend;
                lstArtShow.Columns[e.Column].ImageIndex = ArtShowSortAscend ? 0 : 1;
            }
            else
            {
                lstArtShow.Columns[ArtShowSortColumn].ImageIndex = -1;
                lstArtShow.Columns[ArtShowSortColumn].TextAlign = lstArtShow.Columns[ArtShowSortColumn].TextAlign;
                ArtShowSortAscend = true;
                ArtShowSortColumn = e.Column;
                lstArtShow.Columns[e.Column].ImageIndex = 0;
            }

            lstArtShow.BeginUpdate();
            lstArtShow.ListViewItemSorter = new ListViewItemComparer(e.Column, ArtShowSortAscend);
            lstArtShow.Sort();
            lstArtShow.EndUpdate();
        }

        private void lstPrintShop_ColumnClick(object sender, ColumnClickEventArgs e)
        {
            if (Math.Abs(PrintShopSortColumn) == Math.Abs(e.Column))
            {
                PrintShopSortAscend = !PrintShopSortAscend;
                lstPrintShop.Columns[e.Column].ImageIndex = PrintShopSortAscend ? 0 : 1;
            }
            else
            {
                lstPrintShop.Columns[PrintShopSortColumn].ImageIndex = -1;
                lstPrintShop.Columns[PrintShopSortColumn].TextAlign = lstPrintShop.Columns[PrintShopSortColumn].TextAlign;
                PrintShopSortAscend = true;
                PrintShopSortColumn = e.Column;
                lstPrintShop.Columns[e.Column].ImageIndex = 0;
            }

            lstPrintShop.BeginUpdate();
            lstPrintShop.ListViewItemSorter = new ListViewItemComparer(e.Column, PrintShopSortAscend);
            lstPrintShop.Sort();
            lstPrintShop.EndUpdate();
        }

        private void FrmArtistInventory_Load(object sender, EventArgs e)
        {

        }

        private void BtnPrintShopLabels_Click(object sender, EventArgs e)
        {
            _currentTagPrintNumber = 0;
            var document = new PrintDocument();
            document.PrintPage += PrintShopDocumentOnPrintPage;
            var dialog = new PrintDialog
            {
                Document = document,
                AllowSomePages = true,
                AllowCurrentPage = false,
                AllowSelection = true,
                AllowPrintToFile = true
            };
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                _PrintShopTagsToPrint.AddRange(
                    (from ListViewItem item in lstPrintShop.SelectedItems select (PrintShopItem)item.Tag).ToList());
                document.Print();
            }
        }

        private void BtnPrintShopAllLabels_Click(object sender, EventArgs e)
        {
            _currentTagPrintNumber = 0;
            var document = new PrintDocument();
            document.PrintPage += PrintShopDocumentOnPrintPage;
            var dialog = new PrintDialog
            {
                Document = document,
                AllowSomePages = true,
                AllowCurrentPage = false,
                AllowSelection = true,
                AllowPrintToFile = true
            };
            if (dialog.ShowDialog() == DialogResult.OK)
            {
                _PrintShopTagsToPrint.AddRange(
                    (from ListViewItem item in lstPrintShop.Items select (PrintShopItem)item.Tag).ToList());
                document.Print();
            }
        }

        private void PrintShopDocumentOnPrintPage(object sender, PrintPageEventArgs e)
        {
            var widthInch = e.PageBounds.Width / 8.5;
            var heightInch = e.PageBounds.Height / 11;
            var leftMargin = widthInch * 0.25;
            var topMargin = heightInch * 0.5;
            var tagNumber = 1;
            var row = 1;
            while (tagNumber <= 30)
            {
                var column = tagNumber % 3 == 0 ? 3 : tagNumber % 3;
                var tag = CompressTag(DrawPrintShopLabel(_PrintShopTagsToPrint[0], e));
                e.Graphics.DrawImage(tag,
                    (float)((leftMargin * (column - 1)) + ((column - 1) * 2.5 * widthInch)),
                    (float)(topMargin + ((row - 1) * heightInch)),
                    (float)(2.5 * widthInch),
                    heightInch);
                _currentTagPrintNumber++;
                if (_PrintShopTagsToPrint[0].QuantitySent == _currentTagPrintNumber)
                {
                    _PrintShopTagsToPrint.RemoveAt(0);
                    _currentTagPrintNumber = 0;
                }
                if (_PrintShopTagsToPrint.Count == 0) break;
                if (tagNumber % 3 == 0) row++;
                tagNumber++;
            }
            if (_PrintShopTagsToPrint.Count > 0) e.HasMorePages = true;
        }

        private Bitmap DrawPrintShopLabel(PrintShopItem item, PrintPageEventArgs e)
        {
            var fontTextBold = new Font("Lucida Sans", 12, FontStyle.Bold);
            var fontText = new Font("Lucida Sans", 10);
            var leftCentered = new StringFormat { LineAlignment = StringAlignment.Center, Alignment = StringAlignment.Near };
            var rightCentered = new StringFormat { LineAlignment = StringAlignment.Center, Alignment = StringAlignment.Far };

            var settings = new PrinterSettings();
            var resX = settings.DefaultPageSettings.PrinterResolution.X;
            var resY = settings.DefaultPageSettings.PrinterResolution.Y;
            var imageWidth = (int)(resX * 2.5);
            var imageHeight = resY;

            var image = new Bitmap(imageWidth, imageHeight);
            image.SetResolution(resX, resY);
            var gfx = Graphics.FromImage(image);
            var currentY = 0F;

            var text = item.ShowNumber + ": " + item.Title;
            var textLines = WrapText(text, image.Width, fontTextBold);
            foreach (string line in textLines)
            {
                gfx.DrawString(line, fontTextBold, Brushes.Black, new RectangleF(0, currentY, image.Width, fontTextBold.GetHeight(gfx)), leftCentered);
                currentY += (float)(fontTextBold.GetHeight(gfx) * 1.05);
            }
            if (textLines.Count == 1)
                currentY += (float)(fontTextBold.GetHeight(gfx) * 1.05);
            text = Artist.DisplayName;
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)), leftCentered);
            text = item.Price.ToString("C");
            gfx.DrawString(text, fontText, Brushes.Black, new RectangleF(0, currentY, image.Width, fontText.GetHeight(gfx)), rightCentered);

            gfx.Dispose();

            var resized = new Bitmap(image, image.Width / 6, image.Height / 6);
            return resized;
        }

        private List<string> WrapText(string text, double pixels, Font font)
        {
            string[] originalLines = text.Split(new string[] { " " }, StringSplitOptions.None);

            List<string> wrappedLines = new List<string>();

            StringBuilder actualLine = new StringBuilder();
            double actualWidth = 0;

            foreach (var item in originalLines)
            {
                int w = TextRenderer.MeasureText(item + " ", font).Width * 5; // 5x to deal with resolution.
                actualWidth += w;

                if (actualWidth > pixels)
                {
                    wrappedLines.Add(actualLine.ToString());
                    actualLine.Clear();
                    actualWidth = w;
                }

                actualLine.Append(item + " ");
            }

            if (actualLine.Length > 0)
                wrappedLines.Add(actualLine.ToString());

            return wrappedLines;
        }
    }
}
