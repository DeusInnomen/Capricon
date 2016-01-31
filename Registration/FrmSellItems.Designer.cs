namespace Registration
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmSellItems));
            this.TabPaymentMethods = new System.Windows.Forms.TabControl();
            this.TabCredit = new System.Windows.Forms.TabPage();
            this.txtExpires = new System.Windows.Forms.TextBox();
            this.txtCardNumber = new System.Windows.Forms.TextBox();
            this.label8 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.btnScanCard = new System.Windows.Forms.Button();
            this.PicCards = new System.Windows.Forms.PictureBox();
            this.label10 = new System.Windows.Forms.Label();
            this.txtCVC = new System.Windows.Forms.TextBox();
            this.TabCheck = new System.Windows.Forms.TabPage();
            this.label5 = new System.Windows.Forms.Label();
            this.TxtCheckNumber = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TabCash = new System.Windows.Forms.TabPage();
            this.label3 = new System.Windows.Forms.Label();
            this.label1 = new System.Windows.Forms.Label();
            this.LstItems = new System.Windows.Forms.ListView();
            this.colSaleDescription = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSaleCategory = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSalePrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSaleThrough = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.BtnPurchase = new System.Windows.Forms.Button();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.label2 = new System.Windows.Forms.Label();
            this.LblAmountDue = new System.Windows.Forms.Label();
            this.LstCart = new System.Windows.Forms.ListView();
            this.colCartItem = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colCartDetail = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colCartPrice = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.label6 = new System.Windows.Forms.Label();
            this.BtnAdd = new System.Windows.Forms.Button();
            this.BtnRemoveItem = new System.Windows.Forms.Button();
            this.BtnEditPrice = new System.Windows.Forms.Button();
            this.label11 = new System.Windows.Forms.Label();
            this.TxtCode = new System.Windows.Forms.TextBox();
            this.BtnCode = new System.Windows.Forms.Button();
            this.LblDiscountAmount = new System.Windows.Forms.Label();
            this.LblDiscount = new System.Windows.Forms.Label();
            this.CmbRecipient = new System.Windows.Forms.ComboBox();
            this.label12 = new System.Windows.Forms.Label();
            this.ChkPrintBadges = new System.Windows.Forms.CheckBox();
            this.BtnAddManual = new System.Windows.Forms.Button();
            this.TabPaymentMethods.SuspendLayout();
            this.TabCredit.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).BeginInit();
            this.TabCheck.SuspendLayout();
            this.TabCash.SuspendLayout();
            this.SuspendLayout();
            // 
            // TabPaymentMethods
            // 
            this.TabPaymentMethods.Controls.Add(this.TabCredit);
            this.TabPaymentMethods.Controls.Add(this.TabCheck);
            this.TabPaymentMethods.Controls.Add(this.TabCash);
            this.TabPaymentMethods.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TabPaymentMethods.Location = new System.Drawing.Point(503, 393);
            this.TabPaymentMethods.Name = "TabPaymentMethods";
            this.TabPaymentMethods.SelectedIndex = 0;
            this.TabPaymentMethods.Size = new System.Drawing.Size(321, 200);
            this.TabPaymentMethods.TabIndex = 0;
            this.TabPaymentMethods.SelectedIndexChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // TabCredit
            // 
            this.TabCredit.Controls.Add(this.txtExpires);
            this.TabCredit.Controls.Add(this.txtCardNumber);
            this.TabCredit.Controls.Add(this.label8);
            this.TabCredit.Controls.Add(this.label7);
            this.TabCredit.Controls.Add(this.btnScanCard);
            this.TabCredit.Controls.Add(this.PicCards);
            this.TabCredit.Controls.Add(this.label10);
            this.TabCredit.Controls.Add(this.txtCVC);
            this.TabCredit.Location = new System.Drawing.Point(4, 26);
            this.TabCredit.Name = "TabCredit";
            this.TabCredit.Padding = new System.Windows.Forms.Padding(3);
            this.TabCredit.Size = new System.Drawing.Size(313, 170);
            this.TabCredit.TabIndex = 0;
            this.TabCredit.Text = "Credit Card";
            this.TabCredit.UseVisualStyleBackColor = true;
            // 
            // txtExpires
            // 
            this.txtExpires.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtExpires.Location = new System.Drawing.Point(227, 41);
            this.txtExpires.MaxLength = 4;
            this.txtExpires.Name = "txtExpires";
            this.txtExpires.ReadOnly = true;
            this.txtExpires.Size = new System.Drawing.Size(80, 23);
            this.txtExpires.TabIndex = 53;
            // 
            // txtCardNumber
            // 
            this.txtCardNumber.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCardNumber.Location = new System.Drawing.Point(227, 16);
            this.txtCardNumber.MaxLength = 4;
            this.txtCardNumber.Name = "txtCardNumber";
            this.txtCardNumber.ReadOnly = true;
            this.txtCardNumber.Size = new System.Drawing.Size(80, 23);
            this.txtCardNumber.TabIndex = 52;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(133, 44);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(88, 16);
            this.label8.TabIndex = 51;
            this.label8.Text = "Expires:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(143, 19);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(78, 16);
            this.label7.TabIndex = 50;
            this.label7.Text = "Card #:";
            // 
            // btnScanCard
            // 
            this.btnScanCard.Location = new System.Drawing.Point(10, 23);
            this.btnScanCard.Name = "btnScanCard";
            this.btnScanCard.Size = new System.Drawing.Size(120, 38);
            this.btnScanCard.TabIndex = 49;
            this.btnScanCard.Text = "Scan Card";
            this.btnScanCard.UseVisualStyleBackColor = true;
            this.btnScanCard.Click += new System.EventHandler(this.btnScanCard_Click);
            // 
            // PicCards
            // 
            this.PicCards.Image = global::Registration.Properties.Resources.card_logos;
            this.PicCards.Location = new System.Drawing.Point(24, 113);
            this.PicCards.Name = "PicCards";
            this.PicCards.Size = new System.Drawing.Size(267, 30);
            this.PicCards.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
            this.PicCards.TabIndex = 48;
            this.PicCards.TabStop = false;
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label10.Location = new System.Drawing.Point(173, 73);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(48, 16);
            this.label10.TabIndex = 46;
            this.label10.Text = "CVC:";
            // 
            // txtCVC
            // 
            this.txtCVC.Enabled = false;
            this.txtCVC.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCVC.Location = new System.Drawing.Point(227, 70);
            this.txtCVC.MaxLength = 4;
            this.txtCVC.Name = "txtCVC";
            this.txtCVC.Size = new System.Drawing.Size(80, 23);
            this.txtCVC.TabIndex = 47;
            this.txtCVC.UseSystemPasswordChar = true;
            this.txtCVC.TextChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // TabCheck
            // 
            this.TabCheck.Controls.Add(this.label5);
            this.TabCheck.Controls.Add(this.TxtCheckNumber);
            this.TabCheck.Controls.Add(this.label4);
            this.TabCheck.Location = new System.Drawing.Point(4, 26);
            this.TabCheck.Name = "TabCheck";
            this.TabCheck.Padding = new System.Windows.Forms.Padding(3);
            this.TabCheck.Size = new System.Drawing.Size(313, 170);
            this.TabCheck.TabIndex = 1;
            this.TabCheck.Text = "Check";
            this.TabCheck.UseVisualStyleBackColor = true;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(8, 121);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(88, 16);
            this.label5.TabIndex = 38;
            this.label5.Text = "Check #:";
            // 
            // TxtCheckNumber
            // 
            this.TxtCheckNumber.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCheckNumber.Location = new System.Drawing.Point(102, 118);
            this.TxtCheckNumber.Name = "TxtCheckNumber";
            this.TxtCheckNumber.Size = new System.Drawing.Size(126, 23);
            this.TxtCheckNumber.TabIndex = 39;
            this.TxtCheckNumber.TextChanged += new System.EventHandler(this.DoUpdatePurchaseButton);
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label4.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
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
            this.TabCash.Location = new System.Drawing.Point(4, 26);
            this.TabCash.Name = "TabCash";
            this.TabCash.Size = new System.Drawing.Size(313, 170);
            this.TabCash.TabIndex = 2;
            this.TabCash.Text = "Cash";
            this.TabCash.UseVisualStyleBackColor = true;
            // 
            // label3
            // 
            this.label3.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label3.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(3, 10);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(307, 160);
            this.label3.TabIndex = 2;
            this.label3.Text = "When you have collected the appropriate amount of cash, press the Purchase button" +
    " below.";
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(507, 368);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(313, 22);
            this.label1.TabIndex = 1;
            this.label1.Text = "Payment Method:";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // LstItems
            // 
            this.LstItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colSaleDescription,
            this.colSaleCategory,
            this.colSalePrice,
            this.colSaleThrough});
            this.LstItems.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstItems.FullRowSelect = true;
            this.LstItems.GridLines = true;
            this.LstItems.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
            this.LstItems.HideSelection = false;
            this.LstItems.Location = new System.Drawing.Point(12, 12);
            this.LstItems.MultiSelect = false;
            this.LstItems.Name = "LstItems";
            this.LstItems.Size = new System.Drawing.Size(808, 216);
            this.LstItems.TabIndex = 2;
            this.LstItems.UseCompatibleStateImageBehavior = false;
            this.LstItems.View = System.Windows.Forms.View.Details;
            this.LstItems.SelectedIndexChanged += new System.EventHandler(this.LstItems_SelectedIndexChanged);
            this.LstItems.DoubleClick += new System.EventHandler(this.LstItems_DoubleClick);
            // 
            // colSaleDescription
            // 
            this.colSaleDescription.Text = "Description";
            this.colSaleDescription.Width = 436;
            // 
            // colSaleCategory
            // 
            this.colSaleCategory.Text = "Category";
            this.colSaleCategory.Width = 147;
            // 
            // colSalePrice
            // 
            this.colSalePrice.Text = "Price";
            this.colSalePrice.Width = 96;
            // 
            // colSaleThrough
            // 
            this.colSaleThrough.Text = "Good Until";
            this.colSaleThrough.Width = 125;
            // 
            // BtnPurchase
            // 
            this.BtnPurchase.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnPurchase.Enabled = false;
            this.BtnPurchase.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPurchase.Location = new System.Drawing.Point(694, 605);
            this.BtnPurchase.Name = "BtnPurchase";
            this.BtnPurchase.Size = new System.Drawing.Size(130, 27);
            this.BtnPurchase.TabIndex = 42;
            this.BtnPurchase.Text = "Purchase";
            this.BtnPurchase.UseVisualStyleBackColor = true;
            this.BtnPurchase.Click += new System.EventHandler(this.BtnPurchase_Click);
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(558, 605);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(130, 27);
            this.BtnCancel.TabIndex = 43;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(534, 346);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(129, 22);
            this.label2.TabIndex = 44;
            this.label2.Text = "Total Due:";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            // 
            // LblAmountDue
            // 
            this.LblAmountDue.Font = new System.Drawing.Font("Lucida Console", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblAmountDue.ForeColor = System.Drawing.Color.Green;
            this.LblAmountDue.Location = new System.Drawing.Point(669, 346);
            this.LblAmountDue.Name = "LblAmountDue";
            this.LblAmountDue.Size = new System.Drawing.Size(129, 22);
            this.LblAmountDue.TabIndex = 45;
            this.LblAmountDue.Text = "$0.00";
            this.LblAmountDue.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // LstCart
            // 
            this.LstCart.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colCartItem,
            this.colCartDetail,
            this.colCartPrice});
            this.LstCart.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstCart.FullRowSelect = true;
            this.LstCart.GridLines = true;
            this.LstCart.HeaderStyle = System.Windows.Forms.ColumnHeaderStyle.Nonclickable;
            this.LstCart.HideSelection = false;
            this.LstCart.Location = new System.Drawing.Point(11, 269);
            this.LstCart.Name = "LstCart";
            this.LstCart.Size = new System.Drawing.Size(486, 324);
            this.LstCart.TabIndex = 46;
            this.LstCart.UseCompatibleStateImageBehavior = false;
            this.LstCart.View = System.Windows.Forms.View.Details;
            this.LstCart.SelectedIndexChanged += new System.EventHandler(this.LstCart_SelectedIndexChanged);
            this.LstCart.DoubleClick += new System.EventHandler(this.LstCart_DoubleClick);
            // 
            // colCartItem
            // 
            this.colCartItem.Text = "Item";
            this.colCartItem.Width = 207;
            // 
            // colCartDetail
            // 
            this.colCartDetail.Text = "Details";
            this.colCartDetail.Width = 189;
            // 
            // colCartPrice
            // 
            this.colCartPrice.Text = "Price";
            this.colCartPrice.Width = 86;
            // 
            // label6
            // 
            this.label6.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(12, 244);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(313, 22);
            this.label6.TabIndex = 47;
            this.label6.Text = "Items Being Sold:";
            this.label6.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // BtnAdd
            // 
            this.BtnAdd.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnAdd.Enabled = false;
            this.BtnAdd.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAdd.Location = new System.Drawing.Point(689, 234);
            this.BtnAdd.Name = "BtnAdd";
            this.BtnAdd.Size = new System.Drawing.Size(130, 27);
            this.BtnAdd.TabIndex = 48;
            this.BtnAdd.Text = "Add Selected";
            this.BtnAdd.UseVisualStyleBackColor = true;
            this.BtnAdd.Click += new System.EventHandler(this.BtnAdd_Click);
            // 
            // BtnRemoveItem
            // 
            this.BtnRemoveItem.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnRemoveItem.Enabled = false;
            this.BtnRemoveItem.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnRemoveItem.Location = new System.Drawing.Point(11, 599);
            this.BtnRemoveItem.Name = "BtnRemoveItem";
            this.BtnRemoveItem.Size = new System.Drawing.Size(139, 27);
            this.BtnRemoveItem.TabIndex = 49;
            this.BtnRemoveItem.Text = "Remove Selected";
            this.BtnRemoveItem.UseVisualStyleBackColor = true;
            this.BtnRemoveItem.Click += new System.EventHandler(this.BtnRemoveItem_Click);
            // 
            // BtnEditPrice
            // 
            this.BtnEditPrice.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnEditPrice.Enabled = false;
            this.BtnEditPrice.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnEditPrice.Location = new System.Drawing.Point(156, 599);
            this.BtnEditPrice.Name = "BtnEditPrice";
            this.BtnEditPrice.Size = new System.Drawing.Size(112, 27);
            this.BtnEditPrice.TabIndex = 50;
            this.BtnEditPrice.Text = "Edit Item Price";
            this.BtnEditPrice.UseVisualStyleBackColor = true;
            this.BtnEditPrice.Click += new System.EventHandler(this.BtnEditPrice_Click);
            // 
            // label11
            // 
            this.label11.AutoSize = true;
            this.label11.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label11.Location = new System.Drawing.Point(504, 269);
            this.label11.Name = "label11";
            this.label11.Size = new System.Drawing.Size(318, 16);
            this.label11.TabIndex = 49;
            this.label11.Text = "Promo Code or Gift Certificate:";
            // 
            // TxtCode
            // 
            this.TxtCode.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCode.Location = new System.Drawing.Point(507, 288);
            this.TxtCode.MaxLength = 30;
            this.TxtCode.Name = "TxtCode";
            this.TxtCode.Size = new System.Drawing.Size(176, 23);
            this.TxtCode.TabIndex = 50;
            // 
            // BtnCode
            // 
            this.BtnCode.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCode.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCode.Location = new System.Drawing.Point(689, 286);
            this.BtnCode.Name = "BtnCode";
            this.BtnCode.Size = new System.Drawing.Size(130, 27);
            this.BtnCode.TabIndex = 51;
            this.BtnCode.Text = "Check Code";
            this.BtnCode.UseVisualStyleBackColor = true;
            this.BtnCode.Click += new System.EventHandler(this.BtnCode_Click);
            // 
            // LblDiscountAmount
            // 
            this.LblDiscountAmount.Font = new System.Drawing.Font("Lucida Console", 15.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblDiscountAmount.ForeColor = System.Drawing.Color.Blue;
            this.LblDiscountAmount.Location = new System.Drawing.Point(669, 319);
            this.LblDiscountAmount.Name = "LblDiscountAmount";
            this.LblDiscountAmount.Size = new System.Drawing.Size(129, 22);
            this.LblDiscountAmount.TabIndex = 53;
            this.LblDiscountAmount.Text = "$0.00";
            this.LblDiscountAmount.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            this.LblDiscountAmount.Visible = false;
            // 
            // LblDiscount
            // 
            this.LblDiscount.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblDiscount.Location = new System.Drawing.Point(534, 319);
            this.LblDiscount.Name = "LblDiscount";
            this.LblDiscount.Size = new System.Drawing.Size(129, 22);
            this.LblDiscount.TabIndex = 52;
            this.LblDiscount.Text = "Discount:";
            this.LblDiscount.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.LblDiscount.Visible = false;
            // 
            // CmbRecipient
            // 
            this.CmbRecipient.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbRecipient.FormattingEnabled = true;
            this.CmbRecipient.Location = new System.Drawing.Point(517, 238);
            this.CmbRecipient.Name = "CmbRecipient";
            this.CmbRecipient.Size = new System.Drawing.Size(166, 21);
            this.CmbRecipient.TabIndex = 50;
            this.CmbRecipient.SelectedIndexChanged += new System.EventHandler(this.CmbRecipient_SelectedIndexChanged);
            // 
            // label12
            // 
            this.label12.AutoSize = true;
            this.label12.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(373, 239);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(138, 16);
            this.label12.TabIndex = 49;
            this.label12.Text = "Purchase For:";
            // 
            // ChkPrintBadges
            // 
            this.ChkPrintBadges.Checked = true;
            this.ChkPrintBadges.CheckState = System.Windows.Forms.CheckState.Checked;
            this.ChkPrintBadges.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.ChkPrintBadges.Location = new System.Drawing.Point(424, 602);
            this.ChkPrintBadges.Name = "ChkPrintBadges";
            this.ChkPrintBadges.Size = new System.Drawing.Size(128, 30);
            this.ChkPrintBadges.TabIndex = 54;
            this.ChkPrintBadges.Text = "Print Labels for Badges";
            this.ChkPrintBadges.UseVisualStyleBackColor = true;
            // 
            // BtnAddManual
            // 
            this.BtnAddManual.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnAddManual.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAddManual.Location = new System.Drawing.Point(274, 599);
            this.BtnAddManual.Name = "BtnAddManual";
            this.BtnAddManual.Size = new System.Drawing.Size(139, 27);
            this.BtnAddManual.TabIndex = 55;
            this.BtnAddManual.Text = "Add Other Charge";
            this.BtnAddManual.UseVisualStyleBackColor = true;
            this.BtnAddManual.Click += new System.EventHandler(this.BtnAddManual_Click);
            // 
            // FrmSellItems
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(836, 644);
            this.Controls.Add(this.BtnAddManual);
            this.Controls.Add(this.ChkPrintBadges);
            this.Controls.Add(this.CmbRecipient);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.LblDiscountAmount);
            this.Controls.Add(this.LblDiscount);
            this.Controls.Add(this.BtnCode);
            this.Controls.Add(this.label11);
            this.Controls.Add(this.BtnEditPrice);
            this.Controls.Add(this.TxtCode);
            this.Controls.Add(this.BtnRemoveItem);
            this.Controls.Add(this.BtnAdd);
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
            this.Text = "Items for Sale";
            this.TabPaymentMethods.ResumeLayout(false);
            this.TabCredit.ResumeLayout(false);
            this.TabCredit.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).EndInit();
            this.TabCheck.ResumeLayout(false);
            this.TabCheck.PerformLayout();
            this.TabCash.ResumeLayout(false);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.TabControl TabPaymentMethods;
        private System.Windows.Forms.TabPage TabCredit;
        private System.Windows.Forms.TabPage TabCheck;
        private System.Windows.Forms.TabPage TabCash;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.ListView LstItems;
        private System.Windows.Forms.ColumnHeader colSaleDescription;
        private System.Windows.Forms.ColumnHeader colSaleCategory;
        private System.Windows.Forms.ColumnHeader colSalePrice;
        private System.Windows.Forms.ColumnHeader colSaleThrough;
        private System.Windows.Forms.Button BtnPurchase;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.TextBox txtCVC;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TxtCheckNumber;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label LblAmountDue;
        private System.Windows.Forms.ListView LstCart;
        private System.Windows.Forms.ColumnHeader colCartItem;
        private System.Windows.Forms.ColumnHeader colCartDetail;
        private System.Windows.Forms.ColumnHeader colCartPrice;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.PictureBox PicCards;
        private System.Windows.Forms.Button BtnAdd;
        private System.Windows.Forms.Button BtnRemoveItem;
        private System.Windows.Forms.Button BtnEditPrice;
        private System.Windows.Forms.Label label11;
        private System.Windows.Forms.TextBox TxtCode;
        private System.Windows.Forms.Button BtnCode;
        private System.Windows.Forms.Label LblDiscountAmount;
        private System.Windows.Forms.Label LblDiscount;
        private System.Windows.Forms.ComboBox CmbRecipient;
        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.CheckBox ChkPrintBadges;
        private System.Windows.Forms.Button BtnAddManual;
        private System.Windows.Forms.TextBox txtExpires;
        private System.Windows.Forms.TextBox txtCardNumber;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.Button btnScanCard;
    }
}