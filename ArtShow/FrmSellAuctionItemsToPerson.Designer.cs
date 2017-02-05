namespace ArtShow
{
    partial class FrmSellAuctionItemsToPerson
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmSellAuctionItemsToPerson));
            this.label2 = new System.Windows.Forms.Label();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnPurchase = new System.Windows.Forms.Button();
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
            this.LblAmountDue = new System.Windows.Forms.Label();
            this.LstItems = new System.Windows.Forms.ListView();
            this.colLocation = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colArtist = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colPrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.BtnPrintSummary = new System.Windows.Forms.Button();
            this.LblAmountTax = new System.Windows.Forms.Label();
            this.label9 = new System.Windows.Forms.Label();
            this.LblAmountTotal = new System.Windows.Forms.Label();
            this.label12 = new System.Windows.Forms.Label();
            this.TabPaymentMethods.SuspendLayout();
            this.TabCredit.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).BeginInit();
            this.TabCheck.SuspendLayout();
            this.TabCash.SuspendLayout();
            this.SuspendLayout();
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(547, 13);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(144, 22);
            this.label2.TabIndex = 61;
            this.label2.Text = "Total Price:";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(647, 336);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(98, 27);
            this.BtnCancel.TabIndex = 60;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // BtnPurchase
            // 
            this.BtnPurchase.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnPurchase.Enabled = false;
            this.BtnPurchase.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPurchase.Location = new System.Drawing.Point(751, 336);
            this.BtnPurchase.Name = "BtnPurchase";
            this.BtnPurchase.Size = new System.Drawing.Size(101, 27);
            this.BtnPurchase.TabIndex = 59;
            this.BtnPurchase.Text = "Purchase";
            this.BtnPurchase.UseVisualStyleBackColor = true;
            this.BtnPurchase.Click += new System.EventHandler(this.BtnPurchase_Click);
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(531, 105);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(313, 22);
            this.label1.TabIndex = 58;
            this.label1.Text = "Payment Method:";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // TabPaymentMethods
            // 
            this.TabPaymentMethods.Controls.Add(this.TabCredit);
            this.TabPaymentMethods.Controls.Add(this.TabCheck);
            this.TabPaymentMethods.Controls.Add(this.TabCash);
            this.TabPaymentMethods.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TabPaymentMethods.Location = new System.Drawing.Point(531, 130);
            this.TabPaymentMethods.Name = "TabPaymentMethods";
            this.TabPaymentMethods.SelectedIndex = 0;
            this.TabPaymentMethods.Size = new System.Drawing.Size(321, 200);
            this.TabPaymentMethods.TabIndex = 57;
            this.TabPaymentMethods.SelectedIndexChanged += new System.EventHandler(this.CheckPurchaseButton);
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
            this.TabCredit.Padding = new System.Windows.Forms.Padding(3);
            this.TabCredit.Size = new System.Drawing.Size(313, 167);
            this.TabCredit.TabIndex = 0;
            this.TabCredit.Text = "Credit Card";
            this.TabCredit.UseVisualStyleBackColor = true;
            // 
            // txtExpires
            // 
            this.txtExpires.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtExpires.Location = new System.Drawing.Point(225, 41);
            this.txtExpires.MaxLength = 4;
            this.txtExpires.Name = "txtExpires";
            this.txtExpires.ReadOnly = true;
            this.txtExpires.Size = new System.Drawing.Size(80, 26);
            this.txtExpires.TabIndex = 67;
            // 
            // txtCardNumber
            // 
            this.txtCardNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCardNumber.Location = new System.Drawing.Point(225, 16);
            this.txtCardNumber.MaxLength = 4;
            this.txtCardNumber.Name = "txtCardNumber";
            this.txtCardNumber.ReadOnly = true;
            this.txtCardNumber.Size = new System.Drawing.Size(80, 26);
            this.txtCardNumber.TabIndex = 66;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(131, 44);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(65, 20);
            this.label8.TabIndex = 65;
            this.label8.Text = "Expires:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(141, 19);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(60, 20);
            this.label7.TabIndex = 64;
            this.label7.Text = "Card #:";
            // 
            // btnScanCard
            // 
            this.btnScanCard.Location = new System.Drawing.Point(8, 23);
            this.btnScanCard.Name = "btnScanCard";
            this.btnScanCard.Size = new System.Drawing.Size(120, 38);
            this.btnScanCard.TabIndex = 63;
            this.btnScanCard.Text = "Scan Card";
            this.btnScanCard.UseVisualStyleBackColor = true;
            this.btnScanCard.Click += new System.EventHandler(this.btnScanCard_Click);
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label10.Location = new System.Drawing.Point(171, 73);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(46, 20);
            this.label10.TabIndex = 61;
            this.label10.Text = "CVC:";
            // 
            // txtCVC
            // 
            this.txtCVC.Enabled = false;
            this.txtCVC.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCVC.Location = new System.Drawing.Point(225, 70);
            this.txtCVC.MaxLength = 4;
            this.txtCVC.Name = "txtCVC";
            this.txtCVC.Size = new System.Drawing.Size(80, 26);
            this.txtCVC.TabIndex = 62;
            this.txtCVC.UseSystemPasswordChar = true;
            this.txtCVC.TextChanged += new System.EventHandler(this.CheckPurchaseButton);
            // 
            // PicCards
            // 
            this.PicCards.Image = global::ArtShow.Properties.Resources.card_logos;
            this.PicCards.Location = new System.Drawing.Point(24, 113);
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
            this.TabCheck.Padding = new System.Windows.Forms.Padding(3);
            this.TabCheck.Size = new System.Drawing.Size(313, 167);
            this.TabCheck.TabIndex = 1;
            this.TabCheck.Text = "Check";
            this.TabCheck.UseVisualStyleBackColor = true;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(8, 121);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(71, 20);
            this.label5.TabIndex = 38;
            this.label5.Text = "Check #:";
            // 
            // TxtCheckNumber
            // 
            this.TxtCheckNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCheckNumber.Location = new System.Drawing.Point(102, 118);
            this.TxtCheckNumber.Name = "TxtCheckNumber";
            this.TxtCheckNumber.Size = new System.Drawing.Size(126, 26);
            this.TxtCheckNumber.TabIndex = 39;
            this.TxtCheckNumber.TextChanged += new System.EventHandler(this.CheckPurchaseButton);
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label4.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(3, 5);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(307, 97);
            this.label4.TabIndex = 3;
            this.label4.Text = "Please ensure that the check was written for the appropriate amount, then record " +
    "the check number below. Press Purchase once ready.";
            // 
            // TabCash
            // 
            this.TabCash.Controls.Add(this.label3);
            this.TabCash.Location = new System.Drawing.Point(4, 29);
            this.TabCash.Name = "TabCash";
            this.TabCash.Size = new System.Drawing.Size(313, 167);
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
            this.label3.Size = new System.Drawing.Size(307, 160);
            this.label3.TabIndex = 2;
            this.label3.Text = "When you have collected the appropriate amount of cash, press the Purchase button" +
    " below.";
            // 
            // LblAmountDue
            // 
            this.LblAmountDue.Font = new System.Drawing.Font("Microsoft Sans Serif", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblAmountDue.ForeColor = System.Drawing.Color.Green;
            this.LblAmountDue.Location = new System.Drawing.Point(697, 13);
            this.LblAmountDue.Name = "LblAmountDue";
            this.LblAmountDue.Size = new System.Drawing.Size(129, 22);
            this.LblAmountDue.TabIndex = 62;
            this.LblAmountDue.Text = "$0.00";
            this.LblAmountDue.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // LstItems
            // 
            this.LstItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colLocation,
            this.colNumber,
            this.colTitle,
            this.colArtist,
            this.colPrice});
            this.LstItems.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstItems.FullRowSelect = true;
            this.LstItems.GridLines = true;
            this.LstItems.HideSelection = false;
            this.LstItems.Location = new System.Drawing.Point(12, 12);
            this.LstItems.Name = "LstItems";
            this.LstItems.Size = new System.Drawing.Size(513, 349);
            this.LstItems.SmallImageList = this.SortImages;
            this.LstItems.TabIndex = 63;
            this.LstItems.UseCompatibleStateImageBehavior = false;
            this.LstItems.View = System.Windows.Forms.View.Details;
            this.LstItems.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstItems_ColumnClick);
            // 
            // colLocation
            // 
            this.colLocation.Text = "Location";
            this.colLocation.Width = 76;
            // 
            // colNumber
            // 
            this.colNumber.Text = "#";
            this.colNumber.Width = 43;
            // 
            // colTitle
            // 
            this.colTitle.Text = "Title";
            this.colTitle.Width = 139;
            // 
            // colArtist
            // 
            this.colArtist.Text = "Artist";
            this.colArtist.Width = 126;
            // 
            // colPrice
            // 
            this.colPrice.Text = "Final Price";
            this.colPrice.Width = 101;
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // BtnPrintSummary
            // 
            this.BtnPrintSummary.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnPrintSummary.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintSummary.Location = new System.Drawing.Point(533, 336);
            this.BtnPrintSummary.Name = "BtnPrintSummary";
            this.BtnPrintSummary.Size = new System.Drawing.Size(108, 27);
            this.BtnPrintSummary.TabIndex = 64;
            this.BtnPrintSummary.Text = "Print Summary";
            this.BtnPrintSummary.UseVisualStyleBackColor = true;
            this.BtnPrintSummary.Click += new System.EventHandler(this.BtnPrintSummary_Click);
            // 
            // LblAmountTax
            // 
            this.LblAmountTax.Font = new System.Drawing.Font("Microsoft Sans Serif", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblAmountTax.ForeColor = System.Drawing.Color.Green;
            this.LblAmountTax.Location = new System.Drawing.Point(697, 44);
            this.LblAmountTax.Name = "LblAmountTax";
            this.LblAmountTax.Size = new System.Drawing.Size(129, 22);
            this.LblAmountTax.TabIndex = 66;
            this.LblAmountTax.Text = "$0.00";
            this.LblAmountTax.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label9
            // 
            this.label9.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label9.Location = new System.Drawing.Point(543, 44);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(148, 22);
            this.label9.TabIndex = 65;
            this.label9.Text = "Taxes:";
            this.label9.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            // 
            // LblAmountTotal
            // 
            this.LblAmountTotal.Font = new System.Drawing.Font("Microsoft Sans Serif", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblAmountTotal.ForeColor = System.Drawing.Color.Green;
            this.LblAmountTotal.Location = new System.Drawing.Point(697, 73);
            this.LblAmountTotal.Name = "LblAmountTotal";
            this.LblAmountTotal.Size = new System.Drawing.Size(129, 22);
            this.LblAmountTotal.TabIndex = 68;
            this.LblAmountTotal.Text = "$0.00";
            this.LblAmountTotal.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label12
            // 
            this.label12.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(543, 73);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(148, 22);
            this.label12.TabIndex = 67;
            this.label12.Text = "Total Amount Due:";
            this.label12.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            // 
            // FrmSellAuctionItemsToPerson
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(864, 373);
            this.Controls.Add(this.LblAmountTotal);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.LblAmountTax);
            this.Controls.Add(this.label9);
            this.Controls.Add(this.BtnPrintSummary);
            this.Controls.Add(this.LstItems);
            this.Controls.Add(this.LblAmountDue);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnPurchase);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TabPaymentMethods);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "FrmSellAuctionItemsToPerson";
            this.ShowIcon = false;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Items Won by Purchaser";
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

        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnPurchase;
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
        private System.Windows.Forms.Label LblAmountDue;
        private System.Windows.Forms.ListView LstItems;
        private System.Windows.Forms.ColumnHeader colLocation;
        private System.Windows.Forms.ColumnHeader colTitle;
        private System.Windows.Forms.ColumnHeader colArtist;
        private System.Windows.Forms.ColumnHeader colPrice;
        private System.Windows.Forms.ColumnHeader colNumber;
        private System.Windows.Forms.Button BtnPrintSummary;
        private System.Windows.Forms.TextBox txtExpires;
        private System.Windows.Forms.TextBox txtCardNumber;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.Button btnScanCard;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.TextBox txtCVC;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.Label LblAmountTax;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.Label LblAmountTotal;
        private System.Windows.Forms.Label label12;
    }
}