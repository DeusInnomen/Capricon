namespace ArtShow
{
    partial class FrmShowBidEntry
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmShowBidEntry));
            this.LstItems = new System.Windows.Forms.ListView();
            this.colNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colTitle = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colBuyerNumber = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colFinalBid = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.label1 = new System.Windows.Forms.Label();
            this.TxtTitle = new System.Windows.Forms.TextBox();
            this.TxtNumber = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.TxtBuyerNum = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.TxtFinalBid = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.BtnClose = new System.Windows.Forms.Button();
            this.label5 = new System.Windows.Forms.Label();
            this.ChkAuction = new System.Windows.Forms.CheckBox();
            this.BtnMoveNext = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // LstItems
            // 
            this.LstItems.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colNumber,
            this.colTitle,
            this.colBuyerNumber,
            this.colFinalBid});
            this.LstItems.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstItems.FullRowSelect = true;
            this.LstItems.GridLines = true;
            this.LstItems.HideSelection = false;
            this.LstItems.Location = new System.Drawing.Point(12, 12);
            this.LstItems.MultiSelect = false;
            this.LstItems.Name = "LstItems";
            this.LstItems.Size = new System.Drawing.Size(426, 282);
            this.LstItems.TabIndex = 12;
            this.LstItems.UseCompatibleStateImageBehavior = false;
            this.LstItems.View = System.Windows.Forms.View.Details;
            this.LstItems.SelectedIndexChanged += new System.EventHandler(this.LstItems_SelectedIndexChanged);
            // 
            // colNumber
            // 
            this.colNumber.Text = "#";
            this.colNumber.Width = 44;
            // 
            // colTitle
            // 
            this.colTitle.Text = "Title";
            this.colTitle.Width = 200;
            // 
            // colBuyerNumber
            // 
            this.colBuyerNumber.Text = "Badge #";
            this.colBuyerNumber.Width = 70;
            // 
            // colFinalBid
            // 
            this.colFinalBid.Text = "Final Bid";
            this.colFinalBid.Width = 86;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(477, 41);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(55, 13);
            this.label1.TabIndex = 2;
            this.label1.Text = "Title:";
            // 
            // TxtTitle
            // 
            this.TxtTitle.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtTitle.Location = new System.Drawing.Point(538, 38);
            this.TxtTitle.Name = "TxtTitle";
            this.TxtTitle.ReadOnly = true;
            this.TxtTitle.Size = new System.Drawing.Size(191, 20);
            this.TxtTitle.TabIndex = 3;
            // 
            // TxtNumber
            // 
            this.TxtNumber.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtNumber.Location = new System.Drawing.Point(538, 12);
            this.TxtNumber.Name = "TxtNumber";
            this.TxtNumber.ReadOnly = true;
            this.TxtNumber.Size = new System.Drawing.Size(61, 20);
            this.TxtNumber.TabIndex = 1;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(461, 15);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(71, 13);
            this.label2.TabIndex = 0;
            this.label2.Text = "Piece #:";
            // 
            // TxtBuyerNum
            // 
            this.TxtBuyerNum.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBuyerNum.Location = new System.Drawing.Point(538, 67);
            this.TxtBuyerNum.Name = "TxtBuyerNum";
            this.TxtBuyerNum.Size = new System.Drawing.Size(61, 20);
            this.TxtBuyerNum.TabIndex = 5;
            this.TxtBuyerNum.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtFinalBid_KeyPress);
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(461, 70);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(71, 13);
            this.label3.TabIndex = 4;
            this.label3.Text = "Buyer #:";
            // 
            // TxtFinalBid
            // 
            this.TxtFinalBid.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtFinalBid.Location = new System.Drawing.Point(538, 93);
            this.TxtFinalBid.Name = "TxtFinalBid";
            this.TxtFinalBid.Size = new System.Drawing.Size(95, 20);
            this.TxtFinalBid.TabIndex = 7;
            this.TxtFinalBid.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtFinalBid_KeyPress);
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(445, 96);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(87, 13);
            this.label4.TabIndex = 6;
            this.label4.Text = "Final Bid:";
            // 
            // BtnClose
            // 
            this.BtnClose.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnClose.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnClose.Location = new System.Drawing.Point(592, 267);
            this.BtnClose.Name = "BtnClose";
            this.BtnClose.Size = new System.Drawing.Size(130, 27);
            this.BtnClose.TabIndex = 11;
            this.BtnClose.Text = "Close";
            this.BtnClose.UseVisualStyleBackColor = true;
            this.BtnClose.Click += new System.EventHandler(this.BtnClose_Click);
            // 
            // label5
            // 
            this.label5.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(445, 144);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(285, 65);
            this.label5.TabIndex = 9;
            this.label5.Text = "Press Enter in either entry box to move to the next item. Changes are saved autom" +
    "atically.  Items with green backgrounds are auction pieces.";
            // 
            // ChkAuction
            // 
            this.ChkAuction.AutoSize = true;
            this.ChkAuction.Font = new System.Drawing.Font("Microsoft San Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.ChkAuction.Location = new System.Drawing.Point(538, 119);
            this.ChkAuction.Name = "ChkAuction";
            this.ChkAuction.Size = new System.Drawing.Size(146, 17);
            this.ChkAuction.TabIndex = 8;
            this.ChkAuction.Text = "Went to Auction";
            this.ChkAuction.UseVisualStyleBackColor = true;
            this.ChkAuction.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtFinalBid_KeyPress);
            // 
            // BtnMoveNext
            // 
            this.BtnMoveNext.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnMoveNext.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnMoveNext.Location = new System.Drawing.Point(592, 212);
            this.BtnMoveNext.Name = "BtnMoveNext";
            this.BtnMoveNext.Size = new System.Drawing.Size(130, 27);
            this.BtnMoveNext.TabIndex = 10;
            this.BtnMoveNext.Text = "Move Next";
            this.BtnMoveNext.UseVisualStyleBackColor = true;
            this.BtnMoveNext.Click += new System.EventHandler(this.BtnMoveNext_Click);
            // 
            // FrmShowBidEntry
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(734, 306);
            this.Controls.Add(this.BtnMoveNext);
            this.Controls.Add(this.ChkAuction);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.BtnClose);
            this.Controls.Add(this.TxtFinalBid);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.TxtBuyerNum);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.TxtNumber);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TxtTitle);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.LstItems);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "FrmShowBidEntry";
            this.Text = "Art Show Bid Entry";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.FrmShowBidEntry_FormClosing);
            this.Load += new System.EventHandler(this.FrmShowBidEntry_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.ListView LstItems;
        private System.Windows.Forms.ColumnHeader colNumber;
        private System.Windows.Forms.ColumnHeader colTitle;
        private System.Windows.Forms.ColumnHeader colBuyerNumber;
        private System.Windows.Forms.ColumnHeader colFinalBid;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox TxtTitle;
        private System.Windows.Forms.TextBox TxtNumber;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox TxtBuyerNum;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.TextBox TxtFinalBid;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Button BtnClose;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.CheckBox ChkAuction;
        private System.Windows.Forms.Button BtnMoveNext;
    }
}