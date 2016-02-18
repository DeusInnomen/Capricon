namespace ArtShow
{
    partial class FrmHangingFees
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
            this.label12 = new System.Windows.Forms.Label();
            this.LblFees = new System.Windows.Forms.Label();
            this.TabMethods = new System.Windows.Forms.TabControl();
            this.TabCredit = new System.Windows.Forms.TabPage();
            this.txtExpires = new System.Windows.Forms.TextBox();
            this.txtCardNumber = new System.Windows.Forms.TextBox();
            this.label8 = new System.Windows.Forms.Label();
            this.label7 = new System.Windows.Forms.Label();
            this.btnScanCard = new System.Windows.Forms.Button();
            this.label10 = new System.Windows.Forms.Label();
            this.txtCVC = new System.Windows.Forms.TextBox();
            this.PicCards = new System.Windows.Forms.PictureBox();
            this.CmbPayee = new System.Windows.Forms.ComboBox();
            this.label1 = new System.Windows.Forms.Label();
            this.TabCheck = new System.Windows.Forms.TabPage();
            this.label5 = new System.Windows.Forms.Label();
            this.TxtCheckNumber = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TabCash = new System.Windows.Forms.TabPage();
            this.label3 = new System.Windows.Forms.Label();
            this.TabWaive = new System.Windows.Forms.TabPage();
            this.label6 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnSubmit = new System.Windows.Forms.Button();
            this.label9 = new System.Windows.Forms.Label();
            this.TxtWaiverReason = new System.Windows.Forms.TextBox();
            this.TabMethods.SuspendLayout();
            this.TabCredit.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.PicCards)).BeginInit();
            this.TabCheck.SuspendLayout();
            this.TabCash.SuspendLayout();
            this.TabWaive.SuspendLayout();
            this.SuspendLayout();
            // 
            // label12
            // 
            this.label12.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(9, 9);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(196, 20);
            this.label12.TabIndex = 72;
            this.label12.Text = "Remaining Hanging Fees:";
            // 
            // LblFees
            // 
            this.LblFees.Font = new System.Drawing.Font("Microsoft Sans Serif", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblFees.ForeColor = System.Drawing.Color.Green;
            this.LblFees.Location = new System.Drawing.Point(206, 8);
            this.LblFees.Name = "LblFees";
            this.LblFees.Size = new System.Drawing.Size(121, 20);
            this.LblFees.TabIndex = 73;
            this.LblFees.Text = "$0.00";
            // 
            // TabMethods
            // 
            this.TabMethods.Controls.Add(this.TabCredit);
            this.TabMethods.Controls.Add(this.TabCheck);
            this.TabMethods.Controls.Add(this.TabCash);
            this.TabMethods.Controls.Add(this.TabWaive);
            this.TabMethods.Location = new System.Drawing.Point(12, 63);
            this.TabMethods.Name = "TabMethods";
            this.TabMethods.SelectedIndex = 0;
            this.TabMethods.Size = new System.Drawing.Size(333, 229);
            this.TabMethods.TabIndex = 74;
            this.TabMethods.SelectedIndexChanged += new System.EventHandler(this.SetSubmitButton);
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
            this.TabCredit.Controls.Add(this.CmbPayee);
            this.TabCredit.Controls.Add(this.label1);
            this.TabCredit.Location = new System.Drawing.Point(4, 22);
            this.TabCredit.Name = "TabCredit";
            this.TabCredit.Padding = new System.Windows.Forms.Padding(3, 3, 3, 3);
            this.TabCredit.Size = new System.Drawing.Size(325, 203);
            this.TabCredit.TabIndex = 0;
            this.TabCredit.Text = "Credit Card";
            this.TabCredit.UseVisualStyleBackColor = true;
            // 
            // txtExpires
            // 
            this.txtExpires.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtExpires.Location = new System.Drawing.Point(231, 92);
            this.txtExpires.MaxLength = 4;
            this.txtExpires.Name = "txtExpires";
            this.txtExpires.ReadOnly = true;
            this.txtExpires.Size = new System.Drawing.Size(80, 26);
            this.txtExpires.TabIndex = 75;
            // 
            // txtCardNumber
            // 
            this.txtCardNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCardNumber.Location = new System.Drawing.Point(231, 68);
            this.txtCardNumber.MaxLength = 4;
            this.txtCardNumber.Name = "txtCardNumber";
            this.txtCardNumber.ReadOnly = true;
            this.txtCardNumber.Size = new System.Drawing.Size(80, 26);
            this.txtCardNumber.TabIndex = 74;
            // 
            // label8
            // 
            this.label8.AutoSize = true;
            this.label8.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label8.Location = new System.Drawing.Point(136, 96);
            this.label8.Name = "label8";
            this.label8.Size = new System.Drawing.Size(65, 20);
            this.label8.TabIndex = 73;
            this.label8.Text = "Expires:";
            // 
            // label7
            // 
            this.label7.AutoSize = true;
            this.label7.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label7.Location = new System.Drawing.Point(147, 70);
            this.label7.Name = "label7";
            this.label7.Size = new System.Drawing.Size(60, 20);
            this.label7.TabIndex = 72;
            this.label7.Text = "Card #:";
            // 
            // btnScanCard
            // 
            this.btnScanCard.Location = new System.Drawing.Point(13, 74);
            this.btnScanCard.Name = "btnScanCard";
            this.btnScanCard.Size = new System.Drawing.Size(120, 38);
            this.btnScanCard.TabIndex = 71;
            this.btnScanCard.Text = "Scan Card";
            this.btnScanCard.UseVisualStyleBackColor = true;
            this.btnScanCard.Click += new System.EventHandler(this.btnScanCard_Click);
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label10.Location = new System.Drawing.Point(176, 124);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(46, 20);
            this.label10.TabIndex = 69;
            this.label10.Text = "CVC:";
            // 
            // txtCVC
            // 
            this.txtCVC.Enabled = false;
            this.txtCVC.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtCVC.Location = new System.Drawing.Point(231, 122);
            this.txtCVC.MaxLength = 4;
            this.txtCVC.Name = "txtCVC";
            this.txtCVC.Size = new System.Drawing.Size(80, 26);
            this.txtCVC.TabIndex = 70;
            this.txtCVC.UseSystemPasswordChar = true;
            this.txtCVC.TextChanged += new System.EventHandler(this.SetSubmitButton);
            // 
            // PicCards
            // 
            this.PicCards.Image = global::ArtShow.Properties.Resources.card_logos;
            this.PicCards.Location = new System.Drawing.Point(29, 164);
            this.PicCards.Name = "PicCards";
            this.PicCards.Size = new System.Drawing.Size(267, 30);
            this.PicCards.SizeMode = System.Windows.Forms.PictureBoxSizeMode.AutoSize;
            this.PicCards.TabIndex = 68;
            this.PicCards.TabStop = false;
            // 
            // CmbPayee
            // 
            this.CmbPayee.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.CmbPayee.FormattingEnabled = true;
            this.CmbPayee.Location = new System.Drawing.Point(13, 31);
            this.CmbPayee.Name = "CmbPayee";
            this.CmbPayee.Size = new System.Drawing.Size(298, 24);
            this.CmbPayee.TabIndex = 59;
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(10, 12);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(115, 20);
            this.label1.TabIndex = 58;
            this.label1.Text = "Name on Card:";
            // 
            // TabCheck
            // 
            this.TabCheck.Controls.Add(this.label5);
            this.TabCheck.Controls.Add(this.TxtCheckNumber);
            this.TabCheck.Controls.Add(this.label4);
            this.TabCheck.Location = new System.Drawing.Point(4, 22);
            this.TabCheck.Name = "TabCheck";
            this.TabCheck.Padding = new System.Windows.Forms.Padding(3, 3, 3, 3);
            this.TabCheck.Size = new System.Drawing.Size(325, 203);
            this.TabCheck.TabIndex = 1;
            this.TabCheck.Text = "Check";
            this.TabCheck.UseVisualStyleBackColor = true;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(14, 127);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(71, 20);
            this.label5.TabIndex = 41;
            this.label5.Text = "Check #:";
            // 
            // TxtCheckNumber
            // 
            this.TxtCheckNumber.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtCheckNumber.Location = new System.Drawing.Point(108, 124);
            this.TxtCheckNumber.Name = "TxtCheckNumber";
            this.TxtCheckNumber.Size = new System.Drawing.Size(126, 26);
            this.TxtCheckNumber.TabIndex = 42;
            this.TxtCheckNumber.TextChanged += new System.EventHandler(this.SetSubmitButton);
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label4.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(9, 11);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(307, 141);
            this.label4.TabIndex = 40;
            this.label4.Text = "Please ensure that the check was written for the appropriate amount, then record " +
    "the check number below. Press Submit once ready.";
            // 
            // TabCash
            // 
            this.TabCash.Controls.Add(this.label3);
            this.TabCash.Location = new System.Drawing.Point(4, 22);
            this.TabCash.Name = "TabCash";
            this.TabCash.Size = new System.Drawing.Size(325, 203);
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
            this.label3.Location = new System.Drawing.Point(9, 3);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(307, 200);
            this.label3.TabIndex = 3;
            this.label3.Text = "When you have collected the appropriate amount of cash, press the Submit button b" +
    "elow.";
            // 
            // TabWaive
            // 
            this.TabWaive.Controls.Add(this.label9);
            this.TabWaive.Controls.Add(this.TxtWaiverReason);
            this.TabWaive.Controls.Add(this.label6);
            this.TabWaive.Location = new System.Drawing.Point(4, 22);
            this.TabWaive.Name = "TabWaive";
            this.TabWaive.Size = new System.Drawing.Size(325, 203);
            this.TabWaive.TabIndex = 3;
            this.TabWaive.Text = "Waive Fees";
            this.TabWaive.UseVisualStyleBackColor = true;
            // 
            // label6
            // 
            this.label6.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label6.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(9, 5);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(307, 95);
            this.label6.TabIndex = 3;
            this.label6.Text = "To mark the Hanging Fees as Paid without charging the above amount, enter the rea" +
    "son that the fees are being waived then press the Submit button now.";
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(9, 40);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(301, 20);
            this.label2.TabIndex = 75;
            this.label2.Text = "Method of Handling Remaining Fees:";
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(79, 298);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(130, 27);
            this.BtnCancel.TabIndex = 77;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // BtnSubmit
            // 
            this.BtnSubmit.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSubmit.Enabled = false;
            this.BtnSubmit.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSubmit.Location = new System.Drawing.Point(215, 298);
            this.BtnSubmit.Name = "BtnSubmit";
            this.BtnSubmit.Size = new System.Drawing.Size(130, 27);
            this.BtnSubmit.TabIndex = 76;
            this.BtnSubmit.Text = "Submit";
            this.BtnSubmit.UseVisualStyleBackColor = true;
            this.BtnSubmit.Click += new System.EventHandler(this.BtnSubmit_Click);
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label9.Location = new System.Drawing.Point(9, 104);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(121, 20);
            this.label9.TabIndex = 43;
            this.label9.Text = "Waiver Reason:";
            // 
            // TxtWaiverReason
            // 
            this.TxtWaiverReason.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtWaiverReason.Location = new System.Drawing.Point(13, 127);
            this.TxtWaiverReason.Name = "TxtWaiverReason";
            this.TxtWaiverReason.Size = new System.Drawing.Size(298, 26);
            this.TxtWaiverReason.TabIndex = 44;
            // 
            // FrmHangingFees
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.CancelButton = this.BtnCancel;
            this.ClientSize = new System.Drawing.Size(357, 337);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnSubmit);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TabMethods);
            this.Controls.Add(this.LblFees);
            this.Controls.Add(this.label12);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "FrmHangingFees";
            this.Text = "Resolve Hanging Fees";
            this.TabMethods.ResumeLayout(false);
            this.TabCredit.ResumeLayout(false);
            this.TabCredit.PerformLayout();
            this.TabCheck.ResumeLayout(false);
            this.TabCheck.PerformLayout();
            this.TabCash.ResumeLayout(false);
            this.TabWaive.ResumeLayout(false);
            this.TabWaive.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.Label LblFees;
        private System.Windows.Forms.TabControl TabMethods;
        private System.Windows.Forms.TabPage TabCredit;
        private System.Windows.Forms.TabPage TabCheck;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TabPage TabCash;
        private System.Windows.Forms.TabPage TabWaive;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TxtCheckNumber;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnSubmit;
        private System.Windows.Forms.ComboBox CmbPayee;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox txtExpires;
        private System.Windows.Forms.TextBox txtCardNumber;
        private System.Windows.Forms.Label label8;
        private System.Windows.Forms.Label label7;
        private System.Windows.Forms.Button btnScanCard;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.TextBox txtCVC;
        private System.Windows.Forms.PictureBox PicCards;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.TextBox TxtWaiverReason;
    }
}