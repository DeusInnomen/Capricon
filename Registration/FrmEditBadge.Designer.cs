namespace Registration
{
    partial class FrmEditBadge
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmEditBadge));
            this.label13 = new System.Windows.Forms.Label();
            this.TxtBadgeHolder = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.TxtBadgeNumber = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.TxtBadgeName = new System.Windows.Forms.TextBox();
            this.BtnClose = new System.Windows.Forms.Button();
            this.BtnTransfer = new System.Windows.Forms.Button();
            this.BtnDelete = new System.Windows.Forms.Button();
            this.BtnSave = new System.Windows.Forms.Button();
            this.LblMessage = new System.Windows.Forms.Label();
            this.btnMarkAsPaid = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // label13
            // 
            this.label13.AutoSize = true;
            this.label13.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label13.Location = new System.Drawing.Point(12, 43);
            this.label13.Name = "label13";
            this.label13.Size = new System.Drawing.Size(111, 13);
            this.label13.TabIndex = 32;
            this.label13.Text = "Badge Holder:";
            // 
            // TxtBadgeHolder
            // 
            this.TxtBadgeHolder.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeHolder.Location = new System.Drawing.Point(129, 40);
            this.TxtBadgeHolder.Name = "TxtBadgeHolder";
            this.TxtBadgeHolder.ReadOnly = true;
            this.TxtBadgeHolder.Size = new System.Drawing.Size(291, 20);
            this.TxtBadgeHolder.TabIndex = 33;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(12, 15);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(71, 13);
            this.label1.TabIndex = 34;
            this.label1.Text = "Badge #:";
            // 
            // TxtBadgeNumber
            // 
            this.TxtBadgeNumber.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeNumber.Location = new System.Drawing.Point(90, 12);
            this.TxtBadgeNumber.Name = "TxtBadgeNumber";
            this.TxtBadgeNumber.ReadOnly = true;
            this.TxtBadgeNumber.Size = new System.Drawing.Size(51, 20);
            this.TxtBadgeNumber.TabIndex = 35;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(158, 15);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(95, 13);
            this.label2.TabIndex = 36;
            this.label2.Text = "Badge Name:";
            // 
            // TxtBadgeName
            // 
            this.TxtBadgeName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeName.Location = new System.Drawing.Point(259, 12);
            this.TxtBadgeName.Name = "TxtBadgeName";
            this.TxtBadgeName.Size = new System.Drawing.Size(161, 20);
            this.TxtBadgeName.TabIndex = 37;
            // 
            // BtnClose
            // 
            this.BtnClose.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnClose.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BtnClose.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnClose.Location = new System.Drawing.Point(290, 101);
            this.BtnClose.Name = "BtnClose";
            this.BtnClose.Size = new System.Drawing.Size(130, 27);
            this.BtnClose.TabIndex = 38;
            this.BtnClose.Text = "Close Window";
            this.BtnClose.UseVisualStyleBackColor = true;
            this.BtnClose.Click += new System.EventHandler(this.BtnClose_Click);
            // 
            // BtnTransfer
            // 
            this.BtnTransfer.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnTransfer.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnTransfer.Location = new System.Drawing.Point(12, 68);
            this.BtnTransfer.Name = "BtnTransfer";
            this.BtnTransfer.Size = new System.Drawing.Size(130, 27);
            this.BtnTransfer.TabIndex = 39;
            this.BtnTransfer.Text = "Transfer Badge";
            this.BtnTransfer.UseVisualStyleBackColor = true;
            this.BtnTransfer.Click += new System.EventHandler(this.BtnTransfer_Click);
            // 
            // BtnDelete
            // 
            this.BtnDelete.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnDelete.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnDelete.Location = new System.Drawing.Point(151, 68);
            this.BtnDelete.Name = "BtnDelete";
            this.BtnDelete.Size = new System.Drawing.Size(130, 27);
            this.BtnDelete.TabIndex = 40;
            this.BtnDelete.Text = "Delete Badge";
            this.BtnDelete.UseVisualStyleBackColor = true;
            this.BtnDelete.Click += new System.EventHandler(this.BtnDelete_Click);
            // 
            // BtnSave
            // 
            this.BtnSave.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSave.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSave.Location = new System.Drawing.Point(290, 68);
            this.BtnSave.Name = "BtnSave";
            this.BtnSave.Size = new System.Drawing.Size(130, 27);
            this.BtnSave.TabIndex = 41;
            this.BtnSave.Text = "Save Changes";
            this.BtnSave.UseVisualStyleBackColor = true;
            this.BtnSave.Click += new System.EventHandler(this.BtnSave_Click);
            // 
            // LblMessage
            // 
            this.LblMessage.Font = new System.Drawing.Font("Lucida Console", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblMessage.Location = new System.Drawing.Point(12, 98);
            this.LblMessage.Name = "LblMessage";
            this.LblMessage.Size = new System.Drawing.Size(269, 33);
            this.LblMessage.TabIndex = 42;
            this.LblMessage.TextAlign = System.Drawing.ContentAlignment.BottomLeft;
            // 
            // btnMarkAsPaid
            // 
            this.btnMarkAsPaid.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.btnMarkAsPaid.Enabled = false;
            this.btnMarkAsPaid.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.btnMarkAsPaid.Location = new System.Drawing.Point(11, 101);
            this.btnMarkAsPaid.Name = "btnMarkAsPaid";
            this.btnMarkAsPaid.Size = new System.Drawing.Size(130, 27);
            this.btnMarkAsPaid.TabIndex = 43;
            this.btnMarkAsPaid.Text = "Mark As Paid";
            this.btnMarkAsPaid.UseVisualStyleBackColor = true;
            this.btnMarkAsPaid.Click += new System.EventHandler(this.btnMarkAsPaid_Click);
            // 
            // FrmEditBadge
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.CancelButton = this.BtnClose;
            this.ClientSize = new System.Drawing.Size(432, 140);
            this.Controls.Add(this.btnMarkAsPaid);
            this.Controls.Add(this.LblMessage);
            this.Controls.Add(this.BtnSave);
            this.Controls.Add(this.BtnDelete);
            this.Controls.Add(this.BtnTransfer);
            this.Controls.Add(this.BtnClose);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TxtBadgeName);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TxtBadgeNumber);
            this.Controls.Add(this.label13);
            this.Controls.Add(this.TxtBadgeHolder);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "FrmEditBadge";
            this.Text = "Edit Badge";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label label13;
        private System.Windows.Forms.TextBox TxtBadgeHolder;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox TxtBadgeNumber;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox TxtBadgeName;
        private System.Windows.Forms.Button BtnClose;
        private System.Windows.Forms.Button BtnTransfer;
        private System.Windows.Forms.Button BtnDelete;
        private System.Windows.Forms.Button BtnSave;
        private System.Windows.Forms.Label LblMessage;
        private System.Windows.Forms.Button btnMarkAsPaid;
    }
}