namespace ArtShow
{
    partial class FrmArtistInventory
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmArtistInventory));
            this.label6 = new System.Windows.Forms.Label();
            this.label1 = new System.Windows.Forms.Label();
            this.lstArtShow = new System.Windows.Forms.ListView();
            this.colShowNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowMedia = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowPrintNum = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowBid = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowCategory = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShowCheckedIn = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.MnuArtShow = new System.Windows.Forms.ContextMenuStrip(this.components);
            this.mnuToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.BtnCheckInArtShow = new System.Windows.Forms.Button();
            this.BtnHangingFees = new System.Windows.Forms.Button();
            this.BtnAddToArtShow = new System.Windows.Forms.Button();
            this.BtnEditItemArtShow = new System.Windows.Forms.Button();
            this.lstPrintShop = new System.Windows.Forms.ListView();
            this.colShopNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopMedia = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopQuantity = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopPrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopCategory = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colShopCheckedIn = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.MnuPrintShop = new System.Windows.Forms.ContextMenuStrip(this.components);
            this.BtnEditItemShop = new System.Windows.Forms.Button();
            this.BtnAddItemShop = new System.Windows.Forms.Button();
            this.BtnCheckInShop = new System.Windows.Forms.Button();
            this.BtnClose = new System.Windows.Forms.Button();
            this.label12 = new System.Windows.Forms.Label();
            this.BtnPrintTags = new System.Windows.Forms.Button();
            this.BtnArtShowControl = new System.Windows.Forms.Button();
            this.BtnPrintShopControl = new System.Windows.Forms.Button();
            this.BtnPrintAllTags = new System.Windows.Forms.Button();
            this.BtnDeleteShowItem = new System.Windows.Forms.Button();
            this.BtnDeleteShopItem = new System.Windows.Forms.Button();
            this.BtnShowBids = new System.Windows.Forms.Button();
            this.BtnCheckout = new System.Windows.Forms.Button();
            this.BtnPrintShopAllLabels = new System.Windows.Forms.Button();
            this.BtnPrintShopLabels = new System.Windows.Forms.Button();
            this.MnuArtShow.SuspendLayout();
            this.SuspendLayout();
            // 
            // label6
            // 
            this.label6.Font = new System.Drawing.Font("Lucida Fax", 18F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(13, 9);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(845, 33);
            this.label6.TabIndex = 16;
            this.label6.Text = "Art Show Items";
            this.label6.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Lucida Fax", 18F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(12, 261);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(845, 33);
            this.label1.TabIndex = 17;
            this.label1.Text = "Print Shop Items";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // lstArtShow
            // 
            this.lstArtShow.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colShowNumber,
            this.colShowTitle,
            this.colShowMedia,
            this.colShowPrintNum,
            this.colShowBid,
            this.colShowLocation,
            this.colShowCategory,
            this.colShowCheckedIn});
            this.lstArtShow.ContextMenuStrip = this.MnuArtShow;
            this.lstArtShow.FullRowSelect = true;
            this.lstArtShow.GridLines = true;
            this.lstArtShow.HideSelection = false;
            this.lstArtShow.Location = new System.Drawing.Point(12, 45);
            this.lstArtShow.Name = "lstArtShow";
            this.lstArtShow.Size = new System.Drawing.Size(894, 142);
            this.lstArtShow.SmallImageList = this.SortImages;
            this.lstArtShow.TabIndex = 18;
            this.lstArtShow.UseCompatibleStateImageBehavior = false;
            this.lstArtShow.View = System.Windows.Forms.View.Details;
            this.lstArtShow.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.lstArtShow_ColumnClick);
            this.lstArtShow.DoubleClick += new System.EventHandler(this.lstArtShow_DoubleClick);
            // 
            // colShowNumber
            // 
            this.colShowNumber.Text = "#";
            this.colShowNumber.Width = 40;
            // 
            // colShowTitle
            // 
            this.colShowTitle.Text = "Title";
            this.colShowTitle.Width = 208;
            // 
            // colShowMedia
            // 
            this.colShowMedia.Text = "Original Media";
            this.colShowMedia.Width = 140;
            // 
            // colShowPrintNum
            // 
            this.colShowPrintNum.Text = "Print #";
            // 
            // colShowBid
            // 
            this.colShowBid.Text = "Minimum Bid";
            this.colShowBid.Width = 75;
            // 
            // colShowLocation
            // 
            this.colShowLocation.Text = "Location";
            this.colShowLocation.Width = 95;
            // 
            // colShowCategory
            // 
            this.colShowCategory.Text = "Category";
            this.colShowCategory.Width = 120;
            // 
            // colShowCheckedIn
            // 
            this.colShowCheckedIn.Text = "Checked In?";
            this.colShowCheckedIn.Width = 77;
            // 
            // MnuArtShow
            // 
            this.MnuArtShow.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.mnuToolStripMenuItem});
            this.MnuArtShow.Name = "MnuArtShow";
            this.MnuArtShow.Size = new System.Drawing.Size(100, 26);
            // 
            // mnuToolStripMenuItem
            // 
            this.mnuToolStripMenuItem.Name = "mnuToolStripMenuItem";
            this.mnuToolStripMenuItem.Size = new System.Drawing.Size(99, 22);
            this.mnuToolStripMenuItem.Text = "Mnu";
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // BtnCheckInArtShow
            // 
            this.BtnCheckInArtShow.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCheckInArtShow.Location = new System.Drawing.Point(776, 193);
            this.BtnCheckInArtShow.Name = "BtnCheckInArtShow";
            this.BtnCheckInArtShow.Size = new System.Drawing.Size(130, 27);
            this.BtnCheckInArtShow.TabIndex = 41;
            this.BtnCheckInArtShow.Text = "Toggle Check In";
            this.BtnCheckInArtShow.UseVisualStyleBackColor = true;
            this.BtnCheckInArtShow.Click += new System.EventHandler(this.BtnCheckInArtShow_Click);
            // 
            // BtnHangingFees
            // 
            this.BtnHangingFees.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnHangingFees.Location = new System.Drawing.Point(613, 193);
            this.BtnHangingFees.Name = "BtnHangingFees";
            this.BtnHangingFees.Size = new System.Drawing.Size(157, 27);
            this.BtnHangingFees.TabIndex = 42;
            this.BtnHangingFees.Text = "Resolve Hanging Fees";
            this.BtnHangingFees.UseVisualStyleBackColor = true;
            this.BtnHangingFees.Click += new System.EventHandler(this.BtnHangingFees_Click);
            // 
            // BtnAddToArtShow
            // 
            this.BtnAddToArtShow.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAddToArtShow.Location = new System.Drawing.Point(429, 193);
            this.BtnAddToArtShow.Name = "BtnAddToArtShow";
            this.BtnAddToArtShow.Size = new System.Drawing.Size(130, 27);
            this.BtnAddToArtShow.TabIndex = 43;
            this.BtnAddToArtShow.Text = "Add New Item";
            this.BtnAddToArtShow.UseVisualStyleBackColor = true;
            this.BtnAddToArtShow.Click += new System.EventHandler(this.BtnAddToArtShow_Click);
            // 
            // BtnEditItemArtShow
            // 
            this.BtnEditItemArtShow.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnEditItemArtShow.Location = new System.Drawing.Point(293, 193);
            this.BtnEditItemArtShow.Name = "BtnEditItemArtShow";
            this.BtnEditItemArtShow.Size = new System.Drawing.Size(130, 27);
            this.BtnEditItemArtShow.TabIndex = 44;
            this.BtnEditItemArtShow.Text = "Edit Selected Item";
            this.BtnEditItemArtShow.UseVisualStyleBackColor = true;
            this.BtnEditItemArtShow.Click += new System.EventHandler(this.BtnEditItemArtShow_Click);
            // 
            // lstPrintShop
            // 
            this.lstPrintShop.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colShopNumber,
            this.colShopTitle,
            this.colShopMedia,
            this.colShopQuantity,
            this.colShopPrice,
            this.colShopLocation,
            this.colShopCategory,
            this.colShopCheckedIn});
            this.lstPrintShop.ContextMenuStrip = this.MnuPrintShop;
            this.lstPrintShop.FullRowSelect = true;
            this.lstPrintShop.GridLines = true;
            this.lstPrintShop.HideSelection = false;
            this.lstPrintShop.Location = new System.Drawing.Point(12, 297);
            this.lstPrintShop.Name = "lstPrintShop";
            this.lstPrintShop.Size = new System.Drawing.Size(894, 158);
            this.lstPrintShop.SmallImageList = this.SortImages;
            this.lstPrintShop.TabIndex = 45;
            this.lstPrintShop.UseCompatibleStateImageBehavior = false;
            this.lstPrintShop.View = System.Windows.Forms.View.Details;
            this.lstPrintShop.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.lstPrintShop_ColumnClick);
            this.lstPrintShop.DoubleClick += new System.EventHandler(this.lstPrintShop_DoubleClick);
            // 
            // colShopNumber
            // 
            this.colShopNumber.Text = "#";
            this.colShopNumber.Width = 40;
            // 
            // colShopTitle
            // 
            this.colShopTitle.Text = "Title";
            this.colShopTitle.Width = 208;
            // 
            // colShopMedia
            // 
            this.colShopMedia.Text = "Original Media";
            this.colShopMedia.Width = 140;
            // 
            // colShopQuantity
            // 
            this.colShopQuantity.Text = "# Sent";
            // 
            // colShopPrice
            // 
            this.colShopPrice.Text = "Sale Price";
            this.colShopPrice.Width = 75;
            // 
            // colShopLocation
            // 
            this.colShopLocation.Text = "Location";
            this.colShopLocation.Width = 95;
            // 
            // colShopCategory
            // 
            this.colShopCategory.Text = "Category";
            this.colShopCategory.Width = 120;
            // 
            // colShopCheckedIn
            // 
            this.colShopCheckedIn.Text = "Checked In?";
            this.colShopCheckedIn.Width = 79;
            // 
            // MnuPrintShop
            // 
            this.MnuPrintShop.Name = "MnuPrintShop";
            this.MnuPrintShop.Size = new System.Drawing.Size(61, 4);
            // 
            // BtnEditItemShop
            // 
            this.BtnEditItemShop.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnEditItemShop.Location = new System.Drawing.Point(504, 461);
            this.BtnEditItemShop.Name = "BtnEditItemShop";
            this.BtnEditItemShop.Size = new System.Drawing.Size(130, 27);
            this.BtnEditItemShop.TabIndex = 48;
            this.BtnEditItemShop.Text = "Edit Selected Item";
            this.BtnEditItemShop.UseVisualStyleBackColor = true;
            this.BtnEditItemShop.Click += new System.EventHandler(this.BtnEditItemShop_Click);
            // 
            // BtnAddItemShop
            // 
            this.BtnAddItemShop.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAddItemShop.Location = new System.Drawing.Point(640, 461);
            this.BtnAddItemShop.Name = "BtnAddItemShop";
            this.BtnAddItemShop.Size = new System.Drawing.Size(130, 27);
            this.BtnAddItemShop.TabIndex = 47;
            this.BtnAddItemShop.Text = "Add New Item";
            this.BtnAddItemShop.UseVisualStyleBackColor = true;
            this.BtnAddItemShop.Click += new System.EventHandler(this.BtnAddItemShop_Click);
            // 
            // BtnCheckInShop
            // 
            this.BtnCheckInShop.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCheckInShop.Location = new System.Drawing.Point(776, 461);
            this.BtnCheckInShop.Name = "BtnCheckInShop";
            this.BtnCheckInShop.Size = new System.Drawing.Size(130, 27);
            this.BtnCheckInShop.TabIndex = 46;
            this.BtnCheckInShop.Text = "Toggle Check In";
            this.BtnCheckInShop.UseVisualStyleBackColor = true;
            this.BtnCheckInShop.Click += new System.EventHandler(this.BtnCheckInShop_Click);
            // 
            // BtnClose
            // 
            this.BtnClose.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnClose.Location = new System.Drawing.Point(776, 494);
            this.BtnClose.Name = "BtnClose";
            this.BtnClose.Size = new System.Drawing.Size(130, 27);
            this.BtnClose.TabIndex = 49;
            this.BtnClose.Text = "Close Window";
            this.BtnClose.UseVisualStyleBackColor = true;
            this.BtnClose.Click += new System.EventHandler(this.BtnClose_Click);
            // 
            // label12
            // 
            this.label12.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(14, 12);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(239, 33);
            this.label12.TabIndex = 71;
            this.label12.Text = "A green background indicates that Hanging Fees are paid.";
            // 
            // BtnPrintTags
            // 
            this.BtnPrintTags.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintTags.Location = new System.Drawing.Point(176, 193);
            this.BtnPrintTags.Name = "BtnPrintTags";
            this.BtnPrintTags.Size = new System.Drawing.Size(111, 27);
            this.BtnPrintTags.TabIndex = 73;
            this.BtnPrintTags.Text = "Print Tags";
            this.BtnPrintTags.UseVisualStyleBackColor = true;
            this.BtnPrintTags.Click += new System.EventHandler(this.BtnPrintTags_Click);
            // 
            // BtnArtShowControl
            // 
            this.BtnArtShowControl.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnArtShowControl.Location = new System.Drawing.Point(12, 193);
            this.BtnArtShowControl.Name = "BtnArtShowControl";
            this.BtnArtShowControl.Size = new System.Drawing.Size(158, 27);
            this.BtnArtShowControl.TabIndex = 74;
            this.BtnArtShowControl.Text = "Print Control Sheet";
            this.BtnArtShowControl.UseVisualStyleBackColor = true;
            this.BtnArtShowControl.Click += new System.EventHandler(this.BtnArtShowControl_Click);
            // 
            // BtnPrintShopControl
            // 
            this.BtnPrintShopControl.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintShopControl.Location = new System.Drawing.Point(340, 461);
            this.BtnPrintShopControl.Name = "BtnPrintShopControl";
            this.BtnPrintShopControl.Size = new System.Drawing.Size(158, 27);
            this.BtnPrintShopControl.TabIndex = 75;
            this.BtnPrintShopControl.Text = "Print Control Sheet";
            this.BtnPrintShopControl.UseVisualStyleBackColor = true;
            this.BtnPrintShopControl.Click += new System.EventHandler(this.BtnPrintShopControl_Click);
            // 
            // BtnPrintAllTags
            // 
            this.BtnPrintAllTags.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintAllTags.Location = new System.Drawing.Point(176, 226);
            this.BtnPrintAllTags.Name = "BtnPrintAllTags";
            this.BtnPrintAllTags.Size = new System.Drawing.Size(111, 27);
            this.BtnPrintAllTags.TabIndex = 76;
            this.BtnPrintAllTags.Text = "Print All Tags";
            this.BtnPrintAllTags.UseVisualStyleBackColor = true;
            this.BtnPrintAllTags.Click += new System.EventHandler(this.BtnPrintAllTags_Click);
            // 
            // BtnDeleteShowItem
            // 
            this.BtnDeleteShowItem.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnDeleteShowItem.Location = new System.Drawing.Point(293, 226);
            this.BtnDeleteShowItem.Name = "BtnDeleteShowItem";
            this.BtnDeleteShowItem.Size = new System.Drawing.Size(130, 27);
            this.BtnDeleteShowItem.TabIndex = 77;
            this.BtnDeleteShowItem.Text = "Delete Items";
            this.BtnDeleteShowItem.UseVisualStyleBackColor = true;
            this.BtnDeleteShowItem.Click += new System.EventHandler(this.BtnDeleteShowItem_Click);
            // 
            // BtnDeleteShopItem
            // 
            this.BtnDeleteShopItem.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnDeleteShopItem.Location = new System.Drawing.Point(504, 494);
            this.BtnDeleteShopItem.Name = "BtnDeleteShopItem";
            this.BtnDeleteShopItem.Size = new System.Drawing.Size(130, 27);
            this.BtnDeleteShopItem.TabIndex = 78;
            this.BtnDeleteShopItem.Text = "Delete Items";
            this.BtnDeleteShopItem.UseVisualStyleBackColor = true;
            this.BtnDeleteShopItem.Click += new System.EventHandler(this.BtnDeleteShopItem_Click);
            // 
            // BtnShowBids
            // 
            this.BtnShowBids.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnShowBids.Location = new System.Drawing.Point(12, 226);
            this.BtnShowBids.Name = "BtnShowBids";
            this.BtnShowBids.Size = new System.Drawing.Size(158, 27);
            this.BtnShowBids.TabIndex = 79;
            this.BtnShowBids.Text = "Enter Final Bids";
            this.BtnShowBids.UseVisualStyleBackColor = true;
            this.BtnShowBids.Click += new System.EventHandler(this.BtnShowBids_Click);
            // 
            // BtnCheckout
            // 
            this.BtnCheckout.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCheckout.Location = new System.Drawing.Point(12, 494);
            this.BtnCheckout.Name = "BtnCheckout";
            this.BtnCheckout.Size = new System.Drawing.Size(158, 27);
            this.BtnCheckout.TabIndex = 80;
            this.BtnCheckout.Text = "Check Out Artist";
            this.BtnCheckout.UseVisualStyleBackColor = true;
            this.BtnCheckout.Click += new System.EventHandler(this.BtnCheckout_Click);
            // 
            // BtnPrintShopAllLabels
            // 
            this.BtnPrintShopAllLabels.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintShopAllLabels.Location = new System.Drawing.Point(176, 494);
            this.BtnPrintShopAllLabels.Name = "BtnPrintShopAllLabels";
            this.BtnPrintShopAllLabels.Size = new System.Drawing.Size(158, 27);
            this.BtnPrintShopAllLabels.TabIndex = 84;
            this.BtnPrintShopAllLabels.Text = "Print All Price Labels";
            this.BtnPrintShopAllLabels.UseVisualStyleBackColor = true;
            this.BtnPrintShopAllLabels.Click += new System.EventHandler(this.BtnPrintShopAllLabels_Click);
            // 
            // BtnPrintShopLabels
            // 
            this.BtnPrintShopLabels.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintShopLabels.Location = new System.Drawing.Point(176, 461);
            this.BtnPrintShopLabels.Name = "BtnPrintShopLabels";
            this.BtnPrintShopLabels.Size = new System.Drawing.Size(158, 27);
            this.BtnPrintShopLabels.TabIndex = 83;
            this.BtnPrintShopLabels.Text = "Print Price Labels";
            this.BtnPrintShopLabels.UseVisualStyleBackColor = true;
            this.BtnPrintShopLabels.Click += new System.EventHandler(this.BtnPrintShopLabels_Click);
            // 
            // FrmArtistInventory
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(917, 527);
            this.Controls.Add(this.BtnPrintShopAllLabels);
            this.Controls.Add(this.BtnPrintShopLabels);
            this.Controls.Add(this.BtnCheckout);
            this.Controls.Add(this.BtnShowBids);
            this.Controls.Add(this.BtnDeleteShopItem);
            this.Controls.Add(this.BtnDeleteShowItem);
            this.Controls.Add(this.BtnPrintAllTags);
            this.Controls.Add(this.BtnPrintShopControl);
            this.Controls.Add(this.BtnArtShowControl);
            this.Controls.Add(this.BtnPrintTags);
            this.Controls.Add(this.BtnClose);
            this.Controls.Add(this.BtnEditItemShop);
            this.Controls.Add(this.BtnAddItemShop);
            this.Controls.Add(this.BtnCheckInShop);
            this.Controls.Add(this.lstPrintShop);
            this.Controls.Add(this.BtnEditItemArtShow);
            this.Controls.Add(this.BtnAddToArtShow);
            this.Controls.Add(this.BtnHangingFees);
            this.Controls.Add(this.BtnCheckInArtShow);
            this.Controls.Add(this.lstArtShow);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.label6);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.Name = "FrmArtistInventory";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "-";
            this.Load += new System.EventHandler(this.FrmArtistInventory_Load);
            this.MnuArtShow.ResumeLayout(false);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.ListView lstArtShow;
        private System.Windows.Forms.ColumnHeader colShowNumber;
        private System.Windows.Forms.ColumnHeader colShowTitle;
        private System.Windows.Forms.ColumnHeader colShowMedia;
        private System.Windows.Forms.ColumnHeader colShowPrintNum;
        private System.Windows.Forms.ColumnHeader colShowBid;
        private System.Windows.Forms.ColumnHeader colShowLocation;
        private System.Windows.Forms.ColumnHeader colShowCategory;
        private System.Windows.Forms.ContextMenuStrip MnuArtShow;
        private System.Windows.Forms.ToolStripMenuItem mnuToolStripMenuItem;
        private System.Windows.Forms.Button BtnCheckInArtShow;
        private System.Windows.Forms.Button BtnHangingFees;
        private System.Windows.Forms.Button BtnAddToArtShow;
        private System.Windows.Forms.Button BtnEditItemArtShow;
        private System.Windows.Forms.ListView lstPrintShop;
        private System.Windows.Forms.ColumnHeader colShopNumber;
        private System.Windows.Forms.ColumnHeader colShopTitle;
        private System.Windows.Forms.ColumnHeader colShopMedia;
        private System.Windows.Forms.ColumnHeader colShopQuantity;
        private System.Windows.Forms.ColumnHeader colShopPrice;
        private System.Windows.Forms.ColumnHeader colShopLocation;
        private System.Windows.Forms.ColumnHeader colShopCategory;
        private System.Windows.Forms.ContextMenuStrip MnuPrintShop;
        private System.Windows.Forms.Button BtnEditItemShop;
        private System.Windows.Forms.Button BtnAddItemShop;
        private System.Windows.Forms.Button BtnCheckInShop;
        private System.Windows.Forms.Button BtnClose;
        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.ColumnHeader colShowCheckedIn;
        private System.Windows.Forms.ColumnHeader colShopCheckedIn;
        private System.Windows.Forms.Button BtnPrintTags;
        private System.Windows.Forms.Button BtnArtShowControl;
        private System.Windows.Forms.Button BtnPrintShopControl;
        private System.Windows.Forms.Button BtnPrintAllTags;
        private System.Windows.Forms.Button BtnDeleteShowItem;
        private System.Windows.Forms.Button BtnDeleteShopItem;
        private System.Windows.Forms.Button BtnShowBids;
        private System.Windows.Forms.Button BtnCheckout;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.Button BtnPrintShopAllLabels;
        private System.Windows.Forms.Button BtnPrintShopLabels;
    }
}