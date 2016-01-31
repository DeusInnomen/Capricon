namespace Registration
{
    partial class FrmMain
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmMain));
            this.BtnLookup = new System.Windows.Forms.Button();
            this.BtnSellItems = new System.Windows.Forms.Button();
            this.BtnPrintBadges = new System.Windows.Forms.Button();
            this.BtnPrintSheetsAttendees = new System.Windows.Forms.Button();
            this.BtnPrintSheetsStaff = new System.Windows.Forms.Button();
            this.SuspendLayout();
            // 
            // BtnLookup
            // 
            this.BtnLookup.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnLookup.Location = new System.Drawing.Point(44, 34);
            this.BtnLookup.Name = "BtnLookup";
            this.BtnLookup.Size = new System.Drawing.Size(247, 52);
            this.BtnLookup.TabIndex = 0;
            this.BtnLookup.Text = "Look Up Account";
            this.BtnLookup.UseVisualStyleBackColor = true;
            this.BtnLookup.Click += new System.EventHandler(this.BtnLookup_Click);
            // 
            // BtnSellItems
            // 
            this.BtnSellItems.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSellItems.Location = new System.Drawing.Point(44, 104);
            this.BtnSellItems.Name = "BtnSellItems";
            this.BtnSellItems.Size = new System.Drawing.Size(247, 52);
            this.BtnSellItems.TabIndex = 1;
            this.BtnSellItems.Text = "Sell Items";
            this.BtnSellItems.UseVisualStyleBackColor = true;
            this.BtnSellItems.Click += new System.EventHandler(this.BtnSellItems_Click);
            // 
            // BtnPrintBadges
            // 
            this.BtnPrintBadges.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintBadges.Location = new System.Drawing.Point(44, 174);
            this.BtnPrintBadges.Name = "BtnPrintBadges";
            this.BtnPrintBadges.Size = new System.Drawing.Size(247, 52);
            this.BtnPrintBadges.TabIndex = 2;
            this.BtnPrintBadges.Text = "Print and Edit Badges";
            this.BtnPrintBadges.UseVisualStyleBackColor = true;
            this.BtnPrintBadges.Click += new System.EventHandler(this.BtnPrintBadges_Click);
            // 
            // BtnPrintSheetsAttendees
            // 
            this.BtnPrintSheetsAttendees.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintSheetsAttendees.Location = new System.Drawing.Point(44, 246);
            this.BtnPrintSheetsAttendees.Name = "BtnPrintSheetsAttendees";
            this.BtnPrintSheetsAttendees.Size = new System.Drawing.Size(247, 52);
            this.BtnPrintSheetsAttendees.TabIndex = 3;
            this.BtnPrintSheetsAttendees.Text = "Print Sign-In Sheets for Attendees";
            this.BtnPrintSheetsAttendees.UseVisualStyleBackColor = true;
            this.BtnPrintSheetsAttendees.Click += new System.EventHandler(this.BtnPrintSheetsAttendees_Click);
            // 
            // BtnPrintSheetsStaff
            // 
            this.BtnPrintSheetsStaff.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintSheetsStaff.Location = new System.Drawing.Point(44, 318);
            this.BtnPrintSheetsStaff.Name = "BtnPrintSheetsStaff";
            this.BtnPrintSheetsStaff.Size = new System.Drawing.Size(247, 52);
            this.BtnPrintSheetsStaff.TabIndex = 4;
            this.BtnPrintSheetsStaff.Text = "Print Sign-In Sheets for Staff";
            this.BtnPrintSheetsStaff.UseVisualStyleBackColor = true;
            this.BtnPrintSheetsStaff.Click += new System.EventHandler(this.BtnPrintSheetsStaff_Click);
            // 
            // FrmMain
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(335, 398);
            this.Controls.Add(this.BtnPrintSheetsStaff);
            this.Controls.Add(this.BtnPrintSheetsAttendees);
            this.Controls.Add(this.BtnPrintBadges);
            this.Controls.Add(this.BtnSellItems);
            this.Controls.Add(this.BtnLookup);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.Name = "FrmMain";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Registration Main Menu";
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.Button BtnLookup;
        private System.Windows.Forms.Button BtnSellItems;
        private System.Windows.Forms.Button BtnPrintBadges;
        private System.Windows.Forms.Button BtnPrintSheetsAttendees;
        private System.Windows.Forms.Button BtnPrintSheetsStaff;
    }
}

