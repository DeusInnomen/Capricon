namespace Registration
{
    partial class FrmRelatedPeople
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmRelatedPeople));
            this.LstPeople = new System.Windows.Forms.ListView();
            this.colFirstName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colLastName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colBadgeName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colMainPhone = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colMainType = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colAltPhone = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colAltType = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.BtnCancel = new System.Windows.Forms.Button();
            this.BtnSelect = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // LstPeople
            // 
            this.LstPeople.Anchor = ((System.Windows.Forms.AnchorStyles)(((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.LstPeople.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colFirstName,
            this.colLastName,
            this.colMainPhone,
            this.colMainType,
            this.colAltPhone,
            this.colAltType,
            this.colBadgeName});
            this.LstPeople.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstPeople.FullRowSelect = true;
            this.LstPeople.GridLines = true;
            this.LstPeople.HideSelection = false;
            this.LstPeople.Location = new System.Drawing.Point(12, 64);
            this.LstPeople.MultiSelect = false;
            this.LstPeople.Name = "LstPeople";
            this.LstPeople.Size = new System.Drawing.Size(896, 181);
            this.LstPeople.TabIndex = 15;
            this.LstPeople.UseCompatibleStateImageBehavior = false;
            this.LstPeople.View = System.Windows.Forms.View.Details;
            // 
            // colFirstName
            // 
            this.colFirstName.Text = "First Name";
            this.colFirstName.Width = 143;
            // 
            // colLastName
            // 
            this.colLastName.Text = "Last Name";
            this.colLastName.Width = 159;
            // 
            // colBadgeName
            // 
            this.colBadgeName.Text = "Badge Name";
            this.colBadgeName.Width = 172;
            // 
            // colMainPhone
            // 
            this.colMainPhone.Text = "Main Phone";
            this.colMainPhone.Width = 119;
            // 
            // colMainType
            // 
            this.colMainType.Text = "Main Type";
            this.colMainType.Width = 99;
            // 
            // colAltPhone
            // 
            this.colAltPhone.Text = "Alt. Phone";
            this.colAltPhone.Width = 122;
            // 
            // colAltType
            // 
            this.colAltType.Text = "Alt Type";
            this.colAltType.Width = 78;
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(648, 355);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(127, 27);
            this.BtnCancel.TabIndex = 20;
            this.BtnCancel.Text = "Cancel";
            this.BtnCancel.UseVisualStyleBackColor = true;
            // 
            // BtnSelect
            // 
            this.BtnSelect.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSelect.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSelect.Location = new System.Drawing.Point(781, 355);
            this.BtnSelect.Name = "BtnSelect";
            this.BtnSelect.Size = new System.Drawing.Size(127, 27);
            this.BtnSelect.TabIndex = 19;
            this.BtnSelect.Text = "Select Person";
            this.BtnSelect.UseVisualStyleBackColor = true;
            // 
            // FrmRelatedPeople
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(920, 394);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.BtnSelect);
            this.Controls.Add(this.LstPeople);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "FrmRelatedPeople";
            this.Text = "People on Account for ";
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.ListView LstPeople;
        private System.Windows.Forms.ColumnHeader colFirstName;
        private System.Windows.Forms.ColumnHeader colLastName;
        private System.Windows.Forms.ColumnHeader colMainPhone;
        private System.Windows.Forms.ColumnHeader colMainType;
        private System.Windows.Forms.ColumnHeader colAltPhone;
        private System.Windows.Forms.ColumnHeader colAltType;
        private System.Windows.Forms.ColumnHeader colBadgeName;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Button BtnSelect;
    }
}