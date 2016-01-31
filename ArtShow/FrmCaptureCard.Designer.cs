namespace ArtShow
{
    partial class FrmCaptureCard
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmCaptureCard));
            this.CmbCCYear = new System.Windows.Forms.ComboBox();
            this.label9 = new System.Windows.Forms.Label();
            this.CmbCCMonth = new System.Windows.Forms.ComboBox();
            this.label8 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.TxtCCNumber = new System.Windows.Forms.TextBox();
            this.PicCards = new System.Windows.Forms.PictureBox();
            this.lblSwipe = new System.Windows.Forms.Label();
            this.btnCancel = new System.Windows.Forms.Button();
            this.btnManual = new System.Windows.Forms.Button();
            this.btnUseCard = new System.Windows.Forms.Button();
            this.lblManual = new System.Windows.Forms.Label();
            this.lblBadRead = new System.Windows.Forms.Label();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).BeginInit();
            this.SuspendLayout();
            // 
            // CmbCCYear
            // 
            this.CmbCCYear.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbCCYear.FormattingEnabled = true;
            this.CmbCCYear.Location = new System.Drawing.Point(243, 142);
            this.CmbCCYear.Name = "CmbCCYear";
            this.CmbCCYear.Size = new System.Drawing.Size(70, 21);
            this.CmbCCYear.TabIndex = 54;
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label9.Location = new System.Drawing.Point(219, 145);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(18, 16);
            this.label9.TabIndex = 53;
            this.label9.Text = "/";
            // 
            // CmbCCMonth
            // 
            this.CmbCCMonth.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbCCMonth.FormattingEnabled = true;
            this.CmbCCMonth.Items.AddRange(new object[] {
            "01",
            "02",
            "03",
            "04",
            "05",
            "06",
            "07",
            "08",
            "09",
            "10",
            "11",
            "12"});
            this.CmbCCMonth.Location = new System.Drawing.Point(161, 142);
            this.CmbCCMonth.Name = "CmbCCMonth";
            this.CmbCCMonth.Size = new System.Drawing.Size(52, 21);
            this.CmbCCMonth.TabIndex = 52;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(12, 145);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(118, 16);
            this.label8.TabIndex = 51;
            this.label8.Text = "Expiration:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(12, 116);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(58, 16);
            this.label7.TabIndex = 49;
            this.label7.Text = "Card:";
            // 
            // TxtCCNumber
            // 
            this.TxtCCNumber.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCCNumber.Location = new System.Drawing.Point(76, 113);
            this.TxtCCNumber.MaxLength = 19;
            this.TxtCCNumber.Name = "TxtCCNumber";
            this.TxtCCNumber.Size = new System.Drawing.Size(237, 23);
            this.TxtCCNumber.TabIndex = 50;
            this.TxtCCNumber.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtCCNumber_KeyPress);
            this.TxtCCNumber.Leave += new System.EventHandler(this.TxtCCNumber_Leave);
            // 
            // PicCards
            // 
            this.PicCards.Image = global::ArtShow.Properties.Resources.card_logos;
            this.PicCards.Location = new System.Drawing.Point(66, 34);
            this.PicCards.Name = "PicCards";
            this.PicCards.Size = new System.Drawing.Size(267, 30);
            this.PicCards.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
            this.PicCards.TabIndex = 57;
            this.PicCards.TabStop = false;
            // 
            // lblSwipe
            // 
            this.lblSwipe.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.lblSwipe.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lblSwipe.Location = new System.Drawing.Point(12, 9);
            this.lblSwipe.Name = "lblSwipe";
            this.lblSwipe.Size = new System.Drawing.Size(377, 24);
            this.lblSwipe.TabIndex = 58;
            this.lblSwipe.Text = "Swipe Credit Card now...";
            this.lblSwipe.TextAlign = System.Drawing.ContentAlignment.TopCenter;
            // 
            // btnCancel
            // 
            this.btnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.btnCancel.Location = new System.Drawing.Point(314, 173);
            this.btnCancel.Name = "btnCancel";
            this.btnCancel.Size = new System.Drawing.Size(75, 23);
            this.btnCancel.TabIndex = 59;
            this.btnCancel.Text = "Cancel";
            this.btnCancel.UseVisualStyleBackColor = true;
            this.btnCancel.Click += new System.EventHandler(this.btnCancel_Click);
            // 
            // btnManual
            // 
            this.btnManual.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnManual.Location = new System.Drawing.Point(12, 173);
            this.btnManual.Name = "btnManual";
            this.btnManual.Size = new System.Drawing.Size(89, 23);
            this.btnManual.TabIndex = 60;
            this.btnManual.Text = "Manual Entry";
            this.btnManual.UseVisualStyleBackColor = true;
            this.btnManual.Click += new System.EventHandler(this.btnManual_Click);
            // 
            // btnUseCard
            // 
            this.btnUseCard.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.btnUseCard.Enabled = false;
            this.btnUseCard.Location = new System.Drawing.Point(328, 113);
            this.btnUseCard.Name = "btnUseCard";
            this.btnUseCard.Size = new System.Drawing.Size(61, 48);
            this.btnUseCard.TabIndex = 61;
            this.btnUseCard.Text = "Use This Card";
            this.btnUseCard.UseVisualStyleBackColor = true;
            this.btnUseCard.Click += new System.EventHandler(this.btnUseCard_Click);
            // 
            // lblManual
            // 
            this.lblManual.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.lblManual.Font = new System.Drawing.Font("Microsoft San Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lblManual.Location = new System.Drawing.Point(12, 92);
            this.lblManual.Name = "lblManual";
            this.lblManual.Size = new System.Drawing.Size(377, 24);
            this.lblManual.TabIndex = 62;
            this.lblManual.Text = "Enter Card to Use Manually:";
            this.lblManual.TextAlign = System.Drawing.ContentAlignment.TopCenter;
            this.lblManual.Visible = false;
            // 
            // lblBadRead
            // 
            this.lblBadRead.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.lblBadRead.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lblBadRead.ForeColor = System.Drawing.Color.Red;
            this.lblBadRead.Location = new System.Drawing.Point(132, 173);
            this.lblBadRead.Name = "lblBadRead";
            this.lblBadRead.Size = new System.Drawing.Size(146, 30);
            this.lblBadRead.TabIndex = 64;
            this.lblBadRead.Text = "Card Not Valid, Try Again...";
            this.lblBadRead.TextAlign = System.Drawing.ContentAlignment.TopCenter;
            this.lblBadRead.Visible = false;
            // 
            // FrmCaptureCard
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.CancelButton = this.btnCancel;
            this.ClientSize = new System.Drawing.Size(401, 208);
            this.ControlBox = false;
            this.Controls.Add(this.lblBadRead);
            this.Controls.Add(this.btnUseCard);
            this.Controls.Add(this.btnManual);
            this.Controls.Add(this.btnCancel);
            this.Controls.Add(this.lblSwipe);
            this.Controls.Add(this.PicCards);
            this.Controls.Add(this.CmbCCYear);
            this.Controls.Add(this.label9);
            this.Controls.Add(this.CmbCCMonth);
            this.Controls.Add(this.label8);
            this.Controls.Add(this.label7);
            this.Controls.Add(this.TxtCCNumber);
            this.Controls.Add(this.lblManual);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.Fixed3D;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.KeyPreview = true;
            this.Name = "FrmCaptureCard";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.TopMost = true;
            this.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.FrmCaptureCard_KeyPress);
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.PictureBox PicCards;
        private System.Windows.Forms.ComboBox CmbCCYear;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.ComboBox CmbCCMonth;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.TextBox TxtCCNumber;
        private System.Windows.Forms.Label lblSwipe;
        private System.Windows.Forms.Button btnCancel;
        private System.Windows.Forms.Button btnManual;
        private System.Windows.Forms.Button btnUseCard;
        private System.Windows.Forms.Label lblManual;
        private System.Windows.Forms.Label lblBadRead;
    }
}