namespace Registration
{
    partial class FrmNewRelatedPerson
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
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnSave = new System.Windows.Forms.Button();
            this.CmbPhoneType2 = new System.Windows.Forms.ComboBox();
            this.CmbPhoneType1 = new System.Windows.Forms.ComboBox();
            this.label2 = new System.Windows.Forms.Label();
            this.TxtBadgeName = new System.Windows.Forms.TextBox();
            this.label10 = new System.Windows.Forms.Label();
            this.TxtPhone2 = new System.Windows.Forms.TextBox();
            this.label9 = new System.Windows.Forms.Label();
            this.TxtPhone1 = new System.Windows.Forms.TextBox();
            this.label12 = new System.Windows.Forms.Label();
            this.TxtFirstName = new System.Windows.Forms.TextBox();
            this.label14 = new System.Windows.Forms.Label();
            this.TxtLastName = new System.Windows.Forms.TextBox();
            this.SuspendLayout();
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(329, 128);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(112, 27);
            this.BtnCancel.TabIndex = 112;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // BtnSave
            // 
            this.BtnSave.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSave.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSave.Location = new System.Drawing.Point(447, 128);
            this.BtnSave.Name = "BtnSave";
            this.BtnSave.Size = new System.Drawing.Size(112, 27);
            this.BtnSave.TabIndex = 111;
            this.BtnSave.Text = "Save Person";
            this.BtnSave.UseVisualStyleBackColor = true;
            this.BtnSave.Click += new System.EventHandler(this.BtnSave_Click);
            // 
            // CmbPhoneType2
            // 
            this.CmbPhoneType2.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbPhoneType2.FormattingEnabled = true;
            this.CmbPhoneType2.Items.AddRange(new object[] {
            "",
            "Home",
            "Mobile",
            "Work",
            "Other"});
            this.CmbPhoneType2.Location = new System.Drawing.Point(465, 67);
            this.CmbPhoneType2.Name = "CmbPhoneType2";
            this.CmbPhoneType2.Size = new System.Drawing.Size(90, 21);
            this.CmbPhoneType2.TabIndex = 108;
            // 
            // CmbPhoneType1
            // 
            this.CmbPhoneType1.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbPhoneType1.FormattingEnabled = true;
            this.CmbPhoneType1.Items.AddRange(new object[] {
            "",
            "Home",
            "Mobile",
            "Work",
            "Other"});
            this.CmbPhoneType1.Location = new System.Drawing.Point(465, 40);
            this.CmbPhoneType1.Name = "CmbPhoneType1";
            this.CmbPhoneType1.Size = new System.Drawing.Size(90, 21);
            this.CmbPhoneType1.TabIndex = 105;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(295, 101);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(95, 13);
            this.label2.TabIndex = 109;
            this.label2.Text = "Badge Name:";
            // 
            // TxtBadgeName
            // 
            this.TxtBadgeName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeName.Location = new System.Drawing.Point(394, 98);
            this.TxtBadgeName.Name = "TxtBadgeName";
            this.TxtBadgeName.Size = new System.Drawing.Size(161, 20);
            this.TxtBadgeName.TabIndex = 110;
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label10.Location = new System.Drawing.Point(5, 70);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(207, 13);
            this.label10.TabIndex = 106;
            this.label10.Text = "Phone Number (Secondary):";
            // 
            // TxtPhone2
            // 
            this.TxtPhone2.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtPhone2.Location = new System.Drawing.Point(218, 67);
            this.TxtPhone2.Name = "TxtPhone2";
            this.TxtPhone2.Size = new System.Drawing.Size(188, 20);
            this.TxtPhone2.TabIndex = 107;
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label9.Location = new System.Drawing.Point(21, 44);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(191, 13);
            this.label9.TabIndex = 103;
            this.label9.Text = "Phone Number (Primary):";
            // 
            // TxtPhone1
            // 
            this.TxtPhone1.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtPhone1.Location = new System.Drawing.Point(218, 41);
            this.TxtPhone1.Name = "TxtPhone1";
            this.TxtPhone1.Size = new System.Drawing.Size(188, 20);
            this.TxtPhone1.TabIndex = 104;
            // 
            // label12
            // 
            this.label12.AutoSize = true;
            this.label12.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.Location = new System.Drawing.Point(12, 19);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(95, 13);
            this.label12.TabIndex = 99;
            this.label12.Text = "First Name:";
            // 
            // TxtFirstName
            // 
            this.TxtFirstName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtFirstName.Location = new System.Drawing.Point(113, 12);
            this.TxtFirstName.Name = "TxtFirstName";
            this.TxtFirstName.Size = new System.Drawing.Size(159, 20);
            this.TxtFirstName.TabIndex = 100;
            // 
            // label14
            // 
            this.label14.AutoSize = true;
            this.label14.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label14.Location = new System.Drawing.Point(303, 19);
            this.label14.Name = "label14";
            this.label14.Size = new System.Drawing.Size(87, 13);
            this.label14.TabIndex = 101;
            this.label14.Text = "Last Name:";
            // 
            // TxtLastName
            // 
            this.TxtLastName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtLastName.Location = new System.Drawing.Point(396, 12);
            this.TxtLastName.Name = "TxtLastName";
            this.TxtLastName.Size = new System.Drawing.Size(159, 20);
            this.TxtLastName.TabIndex = 102;
            // 
            // FrmNewRelatedPerson
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(571, 167);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnSave);
            this.Controls.Add(this.CmbPhoneType2);
            this.Controls.Add(this.CmbPhoneType1);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TxtBadgeName);
            this.Controls.Add(this.label10);
            this.Controls.Add(this.TxtPhone2);
            this.Controls.Add(this.label9);
            this.Controls.Add(this.TxtPhone1);
            this.Controls.Add(this.label12);
            this.Controls.Add(this.TxtFirstName);
            this.Controls.Add(this.label14);
            this.Controls.Add(this.TxtLastName);
            this.Name = "FrmNewRelatedPerson";
            this.Text = "Add Related Person...";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnSave;
        private System.Windows.Forms.ComboBox CmbPhoneType2;
        private System.Windows.Forms.ComboBox CmbPhoneType1;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.TextBox TxtBadgeName;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.TextBox TxtPhone2;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.TextBox TxtPhone1;
        private System.Windows.Forms.Label label12;
        private System.Windows.Forms.TextBox TxtFirstName;
        private System.Windows.Forms.Label label14;
        private System.Windows.Forms.TextBox TxtLastName;
    }
}