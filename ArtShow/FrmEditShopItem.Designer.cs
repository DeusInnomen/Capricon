namespace ArtShow
{
    partial class FrmEditShopItem
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
            this.LblShowNumber = new System.Windows.Forms.Label();
            this.label9 = new System.Windows.Forms.Label();
            this.CmbCategory = new System.Windows.Forms.ComboBox();
            this.label8 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.TxtLocation = new System.Windows.Forms.TextBox();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnSave = new System.Windows.Forms.Button();
            this.label6 = new System.Windows.Forms.Label();
            this.TxtNotes = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TxtPrice = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.TxtMedia = new System.Windows.Forms.TextBox();
            this.label12 = new System.Windows.Forms.Label();
            this.TxtTitle = new System.Windows.Forms.TextBox();
            this.label5 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.NumQuantitySent = new System.Windows.Forms.NumericUpDown();
            ((System.ComponentModel.ISupportInitialize)(this.NumQuantitySent)).BeginInit();
            this.SuspendLayout();
            // 
            // LblShowNumber
            // 
            this.LblShowNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblShowNumber.Location = new System.Drawing.Point(76, 327);
            this.LblShowNumber.Name = "LblShowNumber";
            this.LblShowNumber.Size = new System.Drawing.Size(57, 19);
            this.LblShowNumber.TabIndex = 16;
            this.LblShowNumber.Text = "TBD";
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label9.Location = new System.Drawing.Point(12, 330);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(54, 16);
            this.label9.TabIndex = 15;
            this.label9.Text = "Show #:";
            // 
            // CmbCategory
            // 
            this.CmbCategory.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbCategory.FormattingEnabled = true;
            this.CmbCategory.Items.AddRange(new object[] {
            "Not Sold",
            "Live Auction",
            "Direct Sale",
            "Missing",
            "Damaged"});
            this.CmbCategory.Location = new System.Drawing.Point(97, 290);
            this.CmbCategory.Name = "CmbCategory";
            this.CmbCategory.Size = new System.Drawing.Size(172, 21);
            this.CmbCategory.TabIndex = 14;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(12, 294);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(66, 16);
            this.label8.TabIndex = 13;
            this.label8.Text = "Category:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(12, 241);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(113, 16);
            this.label7.TabIndex = 11;
            this.label7.Text = "Location Code(s):";
            // 
            // TxtLocation
            // 
            this.TxtLocation.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.TxtLocation.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtLocation.Location = new System.Drawing.Point(15, 257);
            this.TxtLocation.Name = "TxtLocation";
            this.TxtLocation.Size = new System.Drawing.Size(390, 22);
            this.TxtLocation.TabIndex = 12;
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(139, 322);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(130, 27);
            this.BtnCancel.TabIndex = 18;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // BtnSave
            // 
            this.BtnSave.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSave.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSave.Location = new System.Drawing.Point(275, 322);
            this.BtnSave.Name = "BtnSave";
            this.BtnSave.Size = new System.Drawing.Size(130, 27);
            this.BtnSave.TabIndex = 17;
            this.BtnSave.Text = "Save and Close";
            this.BtnSave.UseVisualStyleBackColor = true;
            this.BtnSave.Click += new System.EventHandler(this.BtnSave_Click);
            // 
            // label6
            // 
            this.label6.AutoSize = true;
            this.label6.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(12, 156);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(110, 16);
            this.label6.TabIndex = 9;
            this.label6.Text = "Additional Notes:";
            // 
            // TxtNotes
            // 
            this.TxtNotes.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.TxtNotes.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtNotes.Location = new System.Drawing.Point(15, 172);
            this.TxtNotes.Multiline = true;
            this.TxtNotes.Name = "TxtNotes";
            this.TxtNotes.Size = new System.Drawing.Size(390, 66);
            this.TxtNotes.TabIndex = 10;
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(293, 102);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(73, 16);
            this.label4.TabIndex = 6;
            this.label4.Text = "Sale Price:";
            // 
            // TxtPrice
            // 
            this.TxtPrice.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.TxtPrice.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtPrice.Location = new System.Drawing.Point(296, 118);
            this.TxtPrice.Name = "TxtPrice";
            this.TxtPrice.Size = new System.Drawing.Size(109, 22);
            this.TxtPrice.TabIndex = 8;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(12, 53);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(98, 16);
            this.label1.TabIndex = 2;
            this.label1.Text = "Original Media:";
            // 
            // TxtMedia
            // 
            this.TxtMedia.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.TxtMedia.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtMedia.Location = new System.Drawing.Point(15, 69);
            this.TxtMedia.Name = "TxtMedia";
            this.TxtMedia.Size = new System.Drawing.Size(390, 22);
            this.TxtMedia.TabIndex = 3;
            // 
            // label12
            // 
            this.label12.AutoSize = true;
            this.label12.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(12, 9);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(37, 16);
            this.label12.TabIndex = 0;
            this.label12.Text = "Title:";
            // 
            // TxtTitle
            // 
            this.TxtTitle.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.TxtTitle.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtTitle.Location = new System.Drawing.Point(15, 25);
            this.TxtTitle.Name = "TxtTitle";
            this.TxtTitle.Size = new System.Drawing.Size(390, 22);
            this.TxtTitle.TabIndex = 1;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(282, 121);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(15, 16);
            this.label5.TabIndex = 7;
            this.label5.Text = "$";
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(12, 102);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(89, 16);
            this.label2.TabIndex = 4;
            this.label2.Text = "Quantity Sent:";
            // 
            // NumQuantitySent
            // 
            this.NumQuantitySent.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.NumQuantitySent.Location = new System.Drawing.Point(15, 118);
            this.NumQuantitySent.Maximum = new decimal(new int[] {
            999,
            0,
            0,
            0});
            this.NumQuantitySent.Minimum = new decimal(new int[] {
            1,
            0,
            0,
            0});
            this.NumQuantitySent.Name = "NumQuantitySent";
            this.NumQuantitySent.Size = new System.Drawing.Size(52, 26);
            this.NumQuantitySent.TabIndex = 5;
            this.NumQuantitySent.Value = new decimal(new int[] {
            1,
            0,
            0,
            0});
            // 
            // FrmEditShopItem
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(417, 361);
            this.ControlBox = false;
            this.Controls.Add(this.NumQuantitySent);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.LblShowNumber);
            this.Controls.Add(this.label9);
            this.Controls.Add(this.CmbCategory);
            this.Controls.Add(this.label8);
            this.Controls.Add(this.label7);
            this.Controls.Add(this.TxtLocation);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnSave);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.TxtNotes);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.TxtPrice);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TxtMedia);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.TxtTitle);
            this.Controls.Add(this.label5);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.Name = "FrmEditShopItem";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Print Shop Item Details";
            ((System.ComponentModel.ISupportInitialize)(this.NumQuantitySent)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label LblShowNumber;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.ComboBox CmbCategory;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.TextBox TxtLocation;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnSave;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.TextBox TxtNotes;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TextBox TxtPrice;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox TxtMedia;
        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.TextBox TxtTitle;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.NumericUpDown NumQuantitySent;
    }
}