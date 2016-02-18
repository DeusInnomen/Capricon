namespace ArtShow
{
    partial class FrmArtistCheckout
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmArtistCheckout));
            this.LstShowItems = new System.Windows.Forms.ListView();
            this.colShowNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowMedia = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowFinalBid = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowClaimed = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.LstShopItems = new System.Windows.Forms.ListView();
            this.colShopNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopMedia = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopQuantityLeft = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopEarned = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopClaimed = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.label6 = new System.Windows.Forms.Label();
            this.label1 = new System.Windows.Forms.Label();
            this.label12 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.LblShowTotal = new System.Windows.Forms.Label();
            this.LblShopTotal = new System.Windows.Forms.Label();
            this.LblConShare = new System.Windows.Forms.Label();
            this.LblTotalOwed = new System.Windows.Forms.Label();
            this.label5 = new System.Windows.Forms.Label();
            this.BtnMarkClaimed = new System.Windows.Forms.Button();
            this.CmbMarkMode = new System.Windows.Forms.ComboBox();
            this.BtnPrintCheckout = new System.Windows.Forms.Button();
            this.LblShippingCost = new System.Windows.Forms.Label();
            this.LblShippingCostText = new System.Windows.Forms.Label();
            this.LblHangingFees = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.SuspendLayout();
            // 
            // LstShowItems
            // 
            this.LstShowItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colShowNumber,
            this.colShowLocation,
            this.colShowTitle,
            this.colShowMedia,
            this.colShowFinalBid,
            this.colShowClaimed});
            this.LstShowItems.FullRowSelect = true;
            this.LstShowItems.GridLines = true;
            this.LstShowItems.HideSelection = false;
            this.LstShowItems.Location = new System.Drawing.Point(12, 36);
            this.LstShowItems.Name = "LstShowItems";
            this.LstShowItems.Size = new System.Drawing.Size(597, 233);
            this.LstShowItems.SmallImageList = this.SortImages;
            this.LstShowItems.TabIndex = 19;
            this.LstShowItems.UseCompatibleStateImageBehavior = false;
            this.LstShowItems.View = System.Windows.Forms.View.Details;
            this.LstShowItems.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstShowItems_ColumnClick);
            // 
            // colShowNumber
            // 
            this.colShowNumber.Text = "#";
            this.colShowNumber.Width = 33;
            // 
            // colShowLocation
            // 
            this.colShowLocation.Text = "Location";
            this.colShowLocation.Width = 58;
            // 
            // colShowTitle
            // 
            this.colShowTitle.Text = "Title";
            this.colShowTitle.Width = 196;
            // 
            // colShowMedia
            // 
            this.colShowMedia.Text = "Original Media";
            this.colShowMedia.Width = 158;
            // 
            // colShowFinalBid
            // 
            this.colShowFinalBid.Text = "Final Bid";
            this.colShowFinalBid.Width = 71;
            // 
            // colShowClaimed
            // 
            this.colShowClaimed.Text = "Claimed";
            this.colShowClaimed.Width = 50;
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // LstShopItems
            // 
            this.LstShopItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colShopNumber,
            this.colShopLocation,
            this.colShopTitle,
            this.colShopMedia,
            this.colShopQuantityLeft,
            this.colShopEarned,
            this.colShopClaimed});
            this.LstShopItems.FullRowSelect = true;
            this.LstShopItems.GridLines = true;
            this.LstShopItems.HideSelection = false;
            this.LstShopItems.Location = new System.Drawing.Point(12, 308);
            this.LstShopItems.Name = "LstShopItems";
            this.LstShopItems.Size = new System.Drawing.Size(597, 194);
            this.LstShopItems.SmallImageList = this.SortImages;
            this.LstShopItems.TabIndex = 46;
            this.LstShopItems.UseCompatibleStateImageBehavior = false;
            this.LstShopItems.View = System.Windows.Forms.View.Details;
            this.LstShopItems.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstShopItems_ColumnClick);
            // 
            // colShopNumber
            // 
            this.colShopNumber.Text = "#";
            this.colShopNumber.Width = 33;
            // 
            // colShopLocation
            // 
            this.colShopLocation.Text = "Location";
            this.colShopLocation.Width = 54;
            // 
            // colShopTitle
            // 
            this.colShopTitle.Text = "Title";
            this.colShopTitle.Width = 165;
            // 
            // colShopMedia
            // 
            this.colShopMedia.Text = "Original Media";
            this.colShopMedia.Width = 114;
            // 
            // colShopQuantityLeft
            // 
            this.colShopQuantityLeft.Text = "# Remaining";
            this.colShopQuantityLeft.Width = 74;
            // 
            // colShopEarned
            // 
            this.colShopEarned.Text = "Total Earned";
            this.colShopEarned.Width = 75;
            // 
            // colShopClaimed
            // 
            this.colShopClaimed.Text = "Claimed";
            this.colShopClaimed.Width = 52;
            // 
            // label6
            // 
            this.label6.Font = new System.Drawing.Font("Lucida Fax", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(12, 9);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(563, 24);
            this.label6.TabIndex = 47;
            this.label6.Text = "Art Show Items";
            this.label6.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Lucida Fax", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(12, 281);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(563, 24);
            this.label1.TabIndex = 48;
            this.label1.Text = "Print Shop Items";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // label12
            // 
            this.label12.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.label12.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(628, 36);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(146, 16);
            this.label12.TabIndex = 72;
            this.label12.Text = "Art Show Total:";
            this.label12.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // label2
            // 
            this.label2.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(615, 60);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(159, 16);
            this.label2.TabIndex = 73;
            this.label2.Text = "Print Shop Total:";
            this.label2.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // label3
            // 
            this.label3.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.label3.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(615, 84);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(159, 16);
            this.label3.TabIndex = 74;
            this.label3.Text = "Convention\'s Share:";
            this.label3.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // LblShowTotal
            // 
            this.LblShowTotal.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblShowTotal.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblShowTotal.ForeColor = System.Drawing.Color.Green;
            this.LblShowTotal.Location = new System.Drawing.Point(780, 36);
            this.LblShowTotal.Name = "LblShowTotal";
            this.LblShowTotal.Size = new System.Drawing.Size(81, 16);
            this.LblShowTotal.TabIndex = 75;
            this.LblShowTotal.Text = "$0.00";
            // 
            // LblShopTotal
            // 
            this.LblShopTotal.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblShopTotal.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblShopTotal.ForeColor = System.Drawing.Color.Green;
            this.LblShopTotal.Location = new System.Drawing.Point(780, 60);
            this.LblShopTotal.Name = "LblShopTotal";
            this.LblShopTotal.Size = new System.Drawing.Size(81, 16);
            this.LblShopTotal.TabIndex = 76;
            this.LblShopTotal.Text = "$0.00";
            // 
            // LblConShare
            // 
            this.LblConShare.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblConShare.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblConShare.ForeColor = System.Drawing.Color.Red;
            this.LblConShare.Location = new System.Drawing.Point(780, 84);
            this.LblConShare.Name = "LblConShare";
            this.LblConShare.Size = new System.Drawing.Size(81, 16);
            this.LblConShare.TabIndex = 77;
            this.LblConShare.Text = "$0.00";
            // 
            // LblTotalOwed
            // 
            this.LblTotalOwed.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblTotalOwed.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblTotalOwed.ForeColor = System.Drawing.Color.Green;
            this.LblTotalOwed.Location = new System.Drawing.Point(780, 175);
            this.LblTotalOwed.Name = "LblTotalOwed";
            this.LblTotalOwed.Size = new System.Drawing.Size(81, 25);
            this.LblTotalOwed.TabIndex = 79;
            this.LblTotalOwed.Text = "$0.00";
            // 
            // label5
            // 
            this.label5.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(615, 175);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(159, 16);
            this.label5.TabIndex = 78;
            this.label5.Text = "Amount Owed:";
            this.label5.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // BtnMarkClaimed
            // 
            this.BtnMarkClaimed.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnMarkClaimed.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnMarkClaimed.Location = new System.Drawing.Point(618, 308);
            this.BtnMarkClaimed.Name = "BtnMarkClaimed";
            this.BtnMarkClaimed.Size = new System.Drawing.Size(140, 27);
            this.BtnMarkClaimed.TabIndex = 80;
            this.BtnMarkClaimed.Text = "Mark as Claimed:";
            this.BtnMarkClaimed.UseVisualStyleBackColor = true;
            this.BtnMarkClaimed.Click += new System.EventHandler(this.BtnMarkClaimed_Click);
            // 
            // CmbMarkMode
            // 
            this.CmbMarkMode.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbMarkMode.FormattingEnabled = true;
            this.CmbMarkMode.Items.AddRange(new object[] {
            "Unsold",
            "Everything"});
            this.CmbMarkMode.Location = new System.Drawing.Point(764, 312);
            this.CmbMarkMode.Name = "CmbMarkMode";
            this.CmbMarkMode.Size = new System.Drawing.Size(97, 21);
            this.CmbMarkMode.TabIndex = 81;
            // 
            // BtnPrintCheckout
            // 
            this.BtnPrintCheckout.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnPrintCheckout.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintCheckout.Location = new System.Drawing.Point(615, 242);
            this.BtnPrintCheckout.Name = "BtnPrintCheckout";
            this.BtnPrintCheckout.Size = new System.Drawing.Size(168, 27);
            this.BtnPrintCheckout.TabIndex = 82;
            this.BtnPrintCheckout.Text = "Print Checkout Sheet";
            this.BtnPrintCheckout.UseVisualStyleBackColor = true;
            this.BtnPrintCheckout.Click += new System.EventHandler(this.BtnPrintCheckout_Click);
            // 
            // LblShippingCost
            // 
            this.LblShippingCost.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblShippingCost.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblShippingCost.ForeColor = System.Drawing.Color.Gray;
            this.LblShippingCost.Location = new System.Drawing.Point(780, 132);
            this.LblShippingCost.Name = "LblShippingCost";
            this.LblShippingCost.Size = new System.Drawing.Size(81, 16);
            this.LblShippingCost.TabIndex = 84;
            this.LblShippingCost.Text = "$0.00";
            // 
            // LblShippingCostText
            // 
            this.LblShippingCostText.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblShippingCostText.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblShippingCostText.Location = new System.Drawing.Point(615, 132);
            this.LblShippingCostText.Name = "LblShippingCostText";
            this.LblShippingCostText.Size = new System.Drawing.Size(159, 16);
            this.LblShippingCostText.TabIndex = 83;
            this.LblShippingCostText.Text = "Shipping Cost:";
            this.LblShippingCostText.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // LblHangingFees
            // 
            this.LblHangingFees.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.LblHangingFees.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblHangingFees.ForeColor = System.Drawing.Color.Red;
            this.LblHangingFees.Location = new System.Drawing.Point(780, 108);
            this.LblHangingFees.Name = "LblHangingFees";
            this.LblHangingFees.Size = new System.Drawing.Size(81, 16);
            this.LblHangingFees.TabIndex = 86;
            this.LblHangingFees.Text = "$0.00";
            // 
            // label7
            // 
            this.label7.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.label7.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(615, 108);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(159, 16);
            this.label7.TabIndex = 85;
            this.label7.Text = "Hanging Fees Due:";
            this.label7.TextAlign = System.Drawing.ContentAlignment.TopRight;
            // 
            // FrmArtistCheckout
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(872, 514);
            this.Controls.Add(this.LblHangingFees);
            this.Controls.Add(this.label7);
            this.Controls.Add(this.LblShippingCost);
            this.Controls.Add(this.LblShippingCostText);
            this.Controls.Add(this.BtnPrintCheckout);
            this.Controls.Add(this.CmbMarkMode);
            this.Controls.Add(this.BtnMarkClaimed);
            this.Controls.Add(this.LblTotalOwed);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.LblConShare);
            this.Controls.Add(this.LblShopTotal);
            this.Controls.Add(this.LblShowTotal);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.LstShopItems);
            this.Controls.Add(this.LstShowItems);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "FrmArtistCheckout";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Artist Checkout";
            this.Load += new System.EventHandler(this.FrmArtistCheckout_Load);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.ListView LstShowItems;
        private System.Windows.Forms.ColumnHeader colShowNumber;
        private System.Windows.Forms.ColumnHeader colShowLocation;
        private System.Windows.Forms.ColumnHeader colShowTitle;
        private System.Windows.Forms.ColumnHeader colShowMedia;
        private System.Windows.Forms.ColumnHeader colShowFinalBid;
        private System.Windows.Forms.ListView LstShopItems;
        private System.Windows.Forms.ColumnHeader colShopNumber;
        private System.Windows.Forms.ColumnHeader colShopTitle;
        private System.Windows.Forms.ColumnHeader colShopMedia;
        private System.Windows.Forms.ColumnHeader colShopQuantityLeft;
        private System.Windows.Forms.ColumnHeader colShopEarned;
        private System.Windows.Forms.ColumnHeader colShopLocation;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label LblShowTotal;
        private System.Windows.Forms.Label LblShopTotal;
        private System.Windows.Forms.Label LblConShare;
        private System.Windows.Forms.Label LblTotalOwed;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.Button BtnMarkClaimed;
        private System.Windows.Forms.ColumnHeader colShowClaimed;
        private System.Windows.Forms.ColumnHeader colShopClaimed;
        private System.Windows.Forms.ComboBox CmbMarkMode;
        private System.Windows.Forms.Button BtnPrintCheckout;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.Label LblShippingCost;
        private System.Windows.Forms.Label LblShippingCostText;
        private System.Windows.Forms.Label LblHangingFees;
        private System.Windows.Forms.Label label7;
    }
}