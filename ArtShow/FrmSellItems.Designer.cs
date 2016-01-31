namespace ArtShow
{
    partial class FrmSellItems
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmSellItems));
            this.BtnClearCart = new System.Windows.Forms.Button();
            this.BtnRemoveItem = new System.Windows.Forms.Button();
            this.label6 = new System.Windows.Forms.Label();
            this.LstCart = new System.Windows.Forms.ListView();
            this.colCartTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colCartArtist = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colCartPrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.LblAmountDue = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnPurchase = new System.Windows.Forms.Button();
            this.LstItems = new System.Windows.Forms.ListView();
            this.colSaleNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSaleTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSaleArtist = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSalePrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSaleQuantity = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.label1 = new System.Windows.Forms.Label();
            this.TabPaymentMethods = new System.Windows.Forms.TabControl();
            this.TabCredit = new System.Windows.Forms.TabPage();
            this.txtExpires = new System.Windows.Forms.TextBox();
            this.txtCardNumber = new System.Windows.Forms.TextBox();
            this.label8 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.btnScanCard = new System.Windows.Forms.Button();
            this.label10 = new System.Windows.Forms.Label();
            this.txtCVC = new System.Windows.Forms.TextBox();
            this.PicCards = new System.Windows.Forms.PictureBox();
            this.TabCheck = new System.Windows.Forms.TabPage();
            this.label5 = new System.Windows.Forms.Label();
            this.TxtCheckNumber = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TabCash = new System.Windows.Forms.TabPage();
            this.label3 = new System.Windows.Forms.Label();
            this.BtnAdd = new System.Windows.Forms.Button();
            this.TabPaymentMethods.SuspendLayout();
            this.TabCredit.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).BeginInit();
            this.TabCheck.SuspendLayout();
            this.TabCash.SuspendLayout();
            this.SuspendLayout();
            // 
            // BtnClearCart
            // 
            this.BtnClearCart.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnClearCart.Enabled = false;
            this.BtnClearCart.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnClearCart.Location = new System.Drawing.Point(148, 604);
            this.BtnClearCart.Name = "BtnClearCart";
            this.BtnClearCart.Size = new System.Drawing.Size(139, 28);
            this.BtnClearCart.TabIndex = 61;
            this.BtnClearCart.Text = "Clear All Items";
            this.BtnClearCart.UseVisualStyleBackColor = true;
            this.BtnClearCart.Click += new System.EventHandler(this.BtnClearCart_Click);
            // 
            // BtnRemoveItem
            // 
            this.BtnRemoveItem.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnRemoveItem.Enabled = false;
            this.BtnRemoveItem.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnRemoveItem.Location = new System.Drawing.Point(3, 604);
            this.BtnRemoveItem.Name = "BtnRemoveItem";
            this.BtnRemoveItem.Size = new System.Drawing.Size(139, 28);
            this.BtnRemoveItem.TabIndex = 60;
            this.BtnRemoveItem.Text = "Remove Selected";
            this.BtnRemoveItem.UseVisualStyleBackColor = true;
            this.BtnRemoveItem.Click += new System.EventHandler(this.BtnRemoveItem_Click);
            // 
            // label6
            // 
            this.label6.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(6, 319);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(313, 23);
            this.label6.TabIndex = 59;
            this.label6.Text = "Items Being Sold:";
            this.label6.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // LstCart
            // 
            this.LstCart.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colCartTitle,
            this.colCartArtist,
            this.colCartPrice});
            this.LstCart.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstCart.FullRowSelect = true;
            this.LstCart.GridLines = true;
            this.LstCart.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
            this.LstCart.HideSelection = false;
            this.LstCart.Location = new System.Drawing.Point(3, 345);
            this.LstCart.Name = "LstCart";
            this.LstCart.Size = new System.Drawing.Size(486, 253);
            this.LstCart.TabIndex = 58;
            this.LstCart.UseCompatibleStateImageBehavior = false;
            this.LstCart.View = System.Windows.Forms.View.Details;
            this.LstCart.SelectedIndexChanged += new System.EventHandler(this.LstCart_SelectedIndexChanged);
            this.LstCart.DoubleClick += new System.EventHandler(this.LstCart_DoubleClick);
            // 
            // colCartTitle
            // 
            this.colCartTitle.Text = "Title";
            this.colCartTitle.Width = 207;
            // 
            // colCartArtist
            // 
            this.colCartArtist.Text = "Artist";
            this.colCartArtist.Width = 189;
            // 
            // colCartPrice
            // 
            this.colCartPrice.Text = "Price";
            this.colCartPrice.Width = 86;
            // 
            // LblAmountDue
            // 
            this.LblAmountDue.Font = new System.Drawing.Font("Microsoft Sans Serif", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblAmountDue.ForeColor = System.Drawing.Color.Green;
            this.LblAmountDue.Location = new System.Drawing.Point(661, 345);
            this.LblAmountDue.Name = "LblAmountDue";
            this.LblAmountDue.Size = new System.Drawing.Size(129, 23);
            this.LblAmountDue.TabIndex = 57;
            this.LblAmountDue.Text = "$0.00";
            this.LblAmountDue.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(526, 345);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(129, 23);
            this.label2.TabIndex = 56;
            this.label2.Text = "Total Due:";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(550, 610);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(130, 28);
            this.BtnCancel.TabIndex = 55;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // BtnPurchase
            // 
            this.BtnPurchase.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnPurchase.Enabled = false;
            this.BtnPurchase.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPurchase.Location = new System.Drawing.Point(686, 610);
            this.BtnPurchase.Name = "BtnPurchase";
            this.BtnPurchase.Size = new System.Drawing.Size(130, 28);
            this.BtnPurchase.TabIndex = 54;
            this.BtnPurchase.Text = "Purchase";
            this.BtnPurchase.UseVisualStyleBackColor = true;
            this.BtnPurchase.Click += new System.EventHandler(this.BtnPurchase_Click);
            // 
            // LstItems
            // 
            this.LstItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colSaleNumber,
            this.colSaleTitle,
            this.colSaleArtist,
            this.colSalePrice,
            this.colSaleQuantity});
            this.LstItems.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstItems.FullRowSelect = true;
            this.LstItems.GridLines = true;
            this.LstItems.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
            this.LstItems.HideSelection = false;
            this.LstItems.Location = new System.Drawing.Point(4, 2);
            this.LstItems.MultiSelect = false;
            this.LstItems.Name = "LstItems";
            this.LstItems.Size = new System.Drawing.Size(808, 305);
            this.LstItems.SmallImageList = this.SortImages;
            this.LstItems.TabIndex = 53;
            this.LstItems.UseCompatibleStateImageBehavior = false;
            this.LstItems.View = System.Windows.Forms.View.Details;
            this.LstItems.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstItems_ColumnClick);
            this.LstItems.SelectedIndexChanged += new System.EventHandler(this.LstItems_SelectedIndexChanged);
            this.LstItems.DoubleClick += new System.EventHandler(this.LstItems_DoubleClick);
            // 
            // colSaleNumber
            // 
            this.colSaleNumber.Text = "Show #";
            this.colSaleNumber.Width = 77;
            // 
            // colSaleTitle
            // 
            this.colSaleTitle.Text = "Title";
            this.colSaleTitle.Width = 381;
            // 
            // colSaleArtist
            // 
            this.colSaleArtist.Text = "Artist";
            this.colSaleArtist.Width = 147;
            // 
            // colSalePrice
            // 
            this.colSalePrice.Text = "Price";
            this.colSalePrice.Width = 102;
            // 
            // colSaleQuantity
            // 
            this.colSaleQuantity.Text = "# Left";
            this.colSaleQuantity.Width = 74;
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(496, 367);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(313, 23);
            this.label1.TabIndex = 52;
            this.label1.Text = "Payment Method:";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // TabPaymentMethods
            // 
            this.TabPaymentMethods.Controls.Add(this.TabCredit);
            this.TabPaymentMethods.Controls.Add(this.TabCheck);
            this.TabPaymentMethods.Controls.Add(this.TabCash);
            this.TabPaymentMethods.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TabPaymentMethods.Location = new System.Drawing.Point(495, 393);
            this.TabPaymentMethods.Name = "TabPaymentMethods";
            this.TabPaymentMethods.SelectedIndex = 0;
            this.TabPaymentMethods.Size = new System.Drawing.Size(321, 205);
            this.TabPaymentMethods.TabIndex = 51;
            this.TabPaymentMethods.SelectedIndexChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // TabCredit
            // 
            this.TabCredit.Controls.Add(this.txtExpires);
            this.TabCredit.Controls.Add(this.txtCardNumber);
            this.TabCredit.Controls.Add(this.label8);
            this.TabCredit.Controls.Add(this.label7);
            this.TabCredit.Controls.Add(this.btnScanCard);
            this.TabCredit.Controls.Add(this.label10);
            this.TabCredit.Controls.Add(this.txtCVC);
            this.TabCredit.Controls.Add(this.PicCards);
            this.TabCredit.Location = new System.Drawing.Point(4, 29);
            this.TabCredit.Name = "TabCredit";
            this.TabCredit.Padding = new System.Windows.Forms.Padding(3, 3, 3, 3);
            this.TabCredit.Size = new System.Drawing.Size(313, 172);
            this.TabCredit.TabIndex = 0;
            this.TabCredit.Text = "Credit Card";
            this.TabCredit.UseVisualStyleBackColor = true;
            // 
            // txtExpires
            // 
            this.txtExpires.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtExpires.Location = new System.Drawing.Point(227, 42);
            this.txtExpires.MaxLength = 4;
            this.txtExpires.Name = "txtExpires";
            this.txtExpires.ReadOnly = true;
            this.txtExpires.Size = new System.Drawing.Size(80, 26);
            this.txtExpires.TabIndex = 60;
            // 
            // txtCardNumber
            // 
            this.txtCardNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCardNumber.Location = new System.Drawing.Point(227, 17);
            this.txtCardNumber.MaxLength = 4;
            this.txtCardNumber.Name = "txtCardNumber";
            this.txtCardNumber.ReadOnly = true;
            this.txtCardNumber.Size = new System.Drawing.Size(80, 26);
            this.txtCardNumber.TabIndex = 59;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(152, 46);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(65, 20);
            this.label8.TabIndex = 58;
            this.label8.Text = "Expires:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(159, 19);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(60, 20);
            this.label7.TabIndex = 57;
            this.label7.Text = "Card #:";
            // 
            // btnScanCard
            // 
            this.btnScanCard.Location = new System.Drawing.Point(15, 34);
            this.btnScanCard.Name = "btnScanCard";
            this.btnScanCard.Size = new System.Drawing.Size(120, 39);
            this.btnScanCard.TabIndex = 56;
            this.btnScanCard.Text = "Scan Card";
            this.btnScanCard.UseVisualStyleBackColor = true;
            this.btnScanCard.Click += new System.EventHandler(this.btnScanCard_Click);
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label10.Location = new System.Drawing.Point(173, 75);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(46, 20);
            this.label10.TabIndex = 54;
            this.label10.Text = "CVC:";
            // 
            // txtCVC
            // 
            this.txtCVC.Enabled = false;
            this.txtCVC.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCVC.Location = new System.Drawing.Point(227, 72);
            this.txtCVC.MaxLength = 4;
            this.txtCVC.Name = "txtCVC";
            this.txtCVC.Size = new System.Drawing.Size(80, 26);
            this.txtCVC.TabIndex = 55;
            this.txtCVC.UseSystemPasswordChar = true;
            this.txtCVC.TextChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // PicCards
            // 
            this.PicCards.Image = global::ArtShow.Properties.Resources.card_logos;
            this.PicCards.Location = new System.Drawing.Point(66, 126);
            this.PicCards.Name = "PicCards";
            this.PicCards.Size = new System.Drawing.Size(267, 30);
            this.PicCards.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
            this.PicCards.TabIndex = 48;
            this.PicCards.TabStop = false;
            // 
            // TabCheck
            // 
            this.TabCheck.Controls.Add(this.label5);
            this.TabCheck.Controls.Add(this.TxtCheckNumber);
            this.TabCheck.Controls.Add(this.label4);
            this.TabCheck.Location = new System.Drawing.Point(4, 29);
            this.TabCheck.Name = "TabCheck";
            this.TabCheck.Padding = new System.Windows.Forms.Padding(3, 3, 3, 3);
            this.TabCheck.Size = new System.Drawing.Size(313, 172);
            this.TabCheck.TabIndex = 1;
            this.TabCheck.Text = "Check";
            this.TabCheck.UseVisualStyleBackColor = true;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(8, 124);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(71, 20);
            this.label5.TabIndex = 38;
            this.label5.Text = "Check #:";
            // 
            // TxtCheckNumber
            // 
            this.TxtCheckNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCheckNumber.Location = new System.Drawing.Point(102, 121);
            this.TxtCheckNumber.Name = "TxtCheckNumber";
            this.TxtCheckNumber.Size = new System.Drawing.Size(126, 26);
            this.TxtCheckNumber.TabIndex = 39;
            this.TxtCheckNumber.TextChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label4.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(3, 5);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(307, 99);
            this.label4.TabIndex = 3;
            this.label4.Text = "Please ensure that the check was written for the appropriate amount, then record " +
    "the check number below. Press Purchase once ready.";
            // 
            // TabCash
            // 
            this.TabCash.Controls.Add(this.label3);
            this.TabCash.Location = new System.Drawing.Point(4, 29);
            this.TabCash.Name = "TabCash";
            this.TabCash.Size = new System.Drawing.Size(313, 172);
            this.TabCash.TabIndex = 2;
            this.TabCash.Text = "Cash";
            this.TabCash.UseVisualStyleBackColor = true;
            // 
            // label3
            // 
            this.label3.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label3.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(3, 10);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(307, 164);
            this.label3.TabIndex = 2;
            this.label3.Text = "When you have collected the appropriate amount of cash, press the Purchase button" +
    " below.";
            // 
            // BtnAdd
            // 
            this.BtnAdd.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnAdd.Enabled = false;
            this.BtnAdd.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAdd.Location = new System.Drawing.Point(682, 314);
            this.BtnAdd.Name = "BtnAdd";
            this.BtnAdd.Size = new System.Drawing.Size(130, 28);
            this.BtnAdd.TabIndex = 49;
            this.BtnAdd.Text = "Add Selected";
            this.BtnAdd.UseVisualStyleBackColor = true;
            this.BtnAdd.Click += new System.EventHandler(this.BtnAdd_Click);
            // 
            // FrmSellItems
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(819, 641);
            this.Controls.Add(this.BtnAdd);
            this.Controls.Add(this.BtnClearCart);
            this.Controls.Add(this.BtnRemoveItem);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.LstCart);
            this.Controls.Add(this.LblAmountDue);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnPurchase);
            this.Controls.Add(this.LstItems);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TabPaymentMethods);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "FrmSellItems";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Sell Print Shop Items";
            this.TabPaymentMethods.ResumeLayout(false);
            this.TabCredit.ResumeLayout(false);
            this.TabCredit.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).EndInit();
            this.TabCheck.ResumeLayout(false);
            this.TabCheck.PerformLayout();
            this.TabCash.ResumeLayout(false);
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Button BtnClearCart;
        private System.Windows.Forms.Button BtnRemoveItem;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.ListView LstCart;
        private System.Windows.Forms.ColumnHeader colCartTitle;
        private System.Windows.Forms.ColumnHeader colCartArtist;
        private System.Windows.Forms.ColumnHeader colCartPrice;
        private System.Windows.Forms.Label LblAmountDue;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnPurchase;
        private System.Windows.Forms.ListView LstItems;
        private System.Windows.Forms.ColumnHeader colSaleNumber;
        private System.Windows.Forms.ColumnHeader colSaleTitle;
        private System.Windows.Forms.ColumnHeader colSaleArtist;
        private System.Windows.Forms.ColumnHeader colSalePrice;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TabControl TabPaymentMethods;
        private System.Windows.Forms.TabPage TabCredit;
        private System.Windows.Forms.PictureBox PicCards;
        private System.Windows.Forms.TabPage TabCheck;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TxtCheckNumber;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TabPage TabCash;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.ColumnHeader colSaleQuantity;
        private System.Windows.Forms.Button BtnAdd;
        private System.Windows.Forms.TextBox txtExpires;
        private System.Windows.Forms.TextBox txtCardNumber;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.Button btnScanCard;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.TextBox txtCVC;
    }
}