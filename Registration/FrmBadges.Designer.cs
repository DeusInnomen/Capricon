namespace Registration
{
    partial class FrmBadges
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmBadges));
            this.LstBadges = new System.Windows.Forms.ListView();
            this.colBadgeType = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colBadgeCount = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.BtnPrintAll = new System.Windows.Forms.Button();
            this.BtnPrintSelected = new System.Windows.Forms.Button();
            this.label3 = new System.Windows.Forms.Label();
            this.TxtBadgeNumber = new System.Windows.Forms.TextBox();
            this.label1 = new System.Windows.Forms.Label();
            this.TxtLastName = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.LstResults = new System.Windows.Forms.ListView();
            this.colSearchNum = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSearchFirstName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSearchLastName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colSearchBadgeName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.SortImages = new System.Windows.Forms.ImageList(this.components);
            this.BtnSearch = new System.Windows.Forms.Button();
            this.label4 = new System.Windows.Forms.Label();
            this.LblStatus = new System.Windows.Forms.Label();
            this.label5 = new System.Windows.Forms.Label();
            this.TxtResumeNum = new System.Windows.Forms.TextBox();
            this.BtnEditBadge = new System.Windows.Forms.Button();
            this.BtnCompBadge = new System.Windows.Forms.Button();
            this.BtnRefresh = new System.Windows.Forms.Button();
            this.rdoOrderNumber = new System.Windows.Forms.RadioButton();
            this.rdoOrderName = new System.Windows.Forms.RadioButton();
            this.CmbPrintOption = new System.Windows.Forms.ComboBox();
            this.SuspendLayout();
            // 
            // LstBadges
            // 
            this.LstBadges.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colBadgeType,
            this.colBadgeCount});
            this.LstBadges.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstBadges.FullRowSelect = true;
            this.LstBadges.GridLines = true;
            this.LstBadges.HideSelection = false;
            this.LstBadges.Location = new System.Drawing.Point(3, 12);
            this.LstBadges.MultiSelect = false;
            this.LstBadges.Name = "LstBadges";
            this.LstBadges.Size = new System.Drawing.Size(266, 148);
            this.LstBadges.TabIndex = 0;
            this.LstBadges.UseCompatibleStateImageBehavior = false;
            this.LstBadges.View = System.Windows.Forms.View.Details;
            this.LstBadges.SelectedIndexChanged += new System.EventHandler(this.LstBadges_SelectedIndexChanged);
            // 
            // colBadgeType
            // 
            this.colBadgeType.Text = "Badge Type";
            this.colBadgeType.Width = 160;
            // 
            // colBadgeCount
            // 
            this.colBadgeCount.Text = "# Badges";
            this.colBadgeCount.Width = 76;
            // 
            // BtnPrintAll
            // 
            this.BtnPrintAll.Enabled = false;
            this.BtnPrintAll.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintAll.Location = new System.Drawing.Point(1, 164);
            this.BtnPrintAll.Name = "BtnPrintAll";
            this.BtnPrintAll.Size = new System.Drawing.Size(266, 27);
            this.BtnPrintAll.TabIndex = 2;
            this.BtnPrintAll.Text = "Print All Badges of Selected Type";
            this.BtnPrintAll.UseVisualStyleBackColor = true;
            this.BtnPrintAll.Click += new System.EventHandler(this.BtnPrintAll_Click);
            // 
            // BtnPrintSelected
            // 
            this.BtnPrintSelected.Enabled = false;
            this.BtnPrintSelected.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnPrintSelected.Location = new System.Drawing.Point(572, 375);
            this.BtnPrintSelected.Name = "BtnPrintSelected";
            this.BtnPrintSelected.Size = new System.Drawing.Size(153, 27);
            this.BtnPrintSelected.TabIndex = 3;
            this.BtnPrintSelected.Text = "Print Selected Badge";
            this.BtnPrintSelected.UseVisualStyleBackColor = true;
            this.BtnPrintSelected.Click += new System.EventHandler(this.BtnPrintSelected_Click);
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(573, 86);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(71, 13);
            this.label3.TabIndex = 11;
            this.label3.Text = "Badge #:";
            // 
            // TxtBadgeNumber
            // 
            this.TxtBadgeNumber.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeNumber.Location = new System.Drawing.Point(650, 83);
            this.TxtBadgeNumber.Name = "TxtBadgeNumber";
            this.TxtBadgeNumber.Size = new System.Drawing.Size(51, 20);
            this.TxtBadgeNumber.TabIndex = 10;
            this.TxtBadgeNumber.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtBadgeNumber_KeyPress);
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(277, 86);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(87, 13);
            this.label1.TabIndex = 9;
            this.label1.Text = "Last Name:";
            // 
            // TxtLastName
            // 
            this.TxtLastName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtLastName.Location = new System.Drawing.Point(370, 83);
            this.TxtLastName.Name = "TxtLastName";
            this.TxtLastName.Size = new System.Drawing.Size(159, 20);
            this.TxtLastName.TabIndex = 8;
            this.TxtLastName.KeyPress += new System.Windows.Forms.KeyPressEventHandler(this.TxtLastName_KeyPress);
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(275, 12);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(450, 78);
            this.label2.TabIndex = 12;
            this.label2.Text = resources.GetString("label2.Text");
            // 
            // LstResults
            // 
            this.LstResults.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colSearchNum,
            this.colSearchFirstName,
            this.colSearchLastName,
            this.colSearchBadgeName});
            this.LstResults.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstResults.FullRowSelect = true;
            this.LstResults.GridLines = true;
            this.LstResults.HideSelection = false;
            this.LstResults.Location = new System.Drawing.Point(275, 109);
            this.LstResults.Name = "LstResults";
            this.LstResults.Size = new System.Drawing.Size(450, 260);
            this.LstResults.SmallImageList = this.SortImages;
            this.LstResults.TabIndex = 13;
            this.LstResults.UseCompatibleStateImageBehavior = false;
            this.LstResults.View = System.Windows.Forms.View.Details;
            this.LstResults.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstResults_ColumnClick);
            this.LstResults.SelectedIndexChanged += new System.EventHandler(this.LstResults_SelectedIndexChanged);
            this.LstResults.DoubleClick += new System.EventHandler(this.LstResults_DoubleClick);
            // 
            // colSearchNum
            // 
            this.colSearchNum.Text = "#";
            this.colSearchNum.Width = 50;
            // 
            // colSearchFirstName
            // 
            this.colSearchFirstName.Text = "First Name";
            this.colSearchFirstName.Width = 118;
            // 
            // colSearchLastName
            // 
            this.colSearchLastName.Text = "Last Name";
            this.colSearchLastName.Width = 103;
            // 
            // colSearchBadgeName
            // 
            this.colSearchBadgeName.Text = "Badge Name";
            this.colSearchBadgeName.Width = 152;
            // 
            // SortImages
            // 
            this.SortImages.ImageStream = ((System.Windows.Forms.ImageListStreamer)(resources.GetObject("SortImages.ImageStream")));
            this.SortImages.TransparentColor = System.Drawing.Color.Transparent;
            this.SortImages.Images.SetKeyName(0, "Down.png");
            this.SortImages.Images.SetKeyName(1, "Up.png");
            // 
            // BtnSearch
            // 
            this.BtnSearch.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSearch.Location = new System.Drawing.Point(286, 375);
            this.BtnSearch.Name = "BtnSearch";
            this.BtnSearch.Size = new System.Drawing.Size(131, 27);
            this.BtnSearch.TabIndex = 14;
            this.BtnSearch.Text = "Search Badges";
            this.BtnSearch.UseVisualStyleBackColor = true;
            this.BtnSearch.Click += new System.EventHandler(this.BtnSearch_Click);
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(49, 300);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(175, 13);
            this.label4.TabIndex = 15;
            this.label4.Text = "Label Printer Status:";
            // 
            // LblStatus
            // 
            this.LblStatus.Font = new System.Drawing.Font("Lucida Console", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LblStatus.Location = new System.Drawing.Point(9, 319);
            this.LblStatus.Name = "LblStatus";
            this.LblStatus.Size = new System.Drawing.Size(259, 49);
            this.LblStatus.TabIndex = 16;
            this.LblStatus.Text = "Detecting...";
            this.LblStatus.TextAlign = System.Drawing.ContentAlignment.TopCenter;
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(9, 200);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(215, 13);
            this.label5.TabIndex = 18;
            this.label5.Text = "Resume Printing at Badge #";
            // 
            // TxtResumeNum
            // 
            this.TxtResumeNum.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtResumeNum.Location = new System.Drawing.Point(226, 198);
            this.TxtResumeNum.Name = "TxtResumeNum";
            this.TxtResumeNum.Size = new System.Drawing.Size(43, 20);
            this.TxtResumeNum.TabIndex = 17;
            // 
            // BtnEditBadge
            // 
            this.BtnEditBadge.Enabled = false;
            this.BtnEditBadge.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnEditBadge.Location = new System.Drawing.Point(430, 375);
            this.BtnEditBadge.Name = "BtnEditBadge";
            this.BtnEditBadge.Size = new System.Drawing.Size(130, 27);
            this.BtnEditBadge.TabIndex = 19;
            this.BtnEditBadge.Text = "Edit Badge";
            this.BtnEditBadge.UseVisualStyleBackColor = true;
            this.BtnEditBadge.Click += new System.EventHandler(this.BtnEditBadge_Click);
            // 
            // BtnCompBadge
            // 
            this.BtnCompBadge.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCompBadge.Location = new System.Drawing.Point(131, 375);
            this.BtnCompBadge.Name = "BtnCompBadge";
            this.BtnCompBadge.Size = new System.Drawing.Size(145, 27);
            this.BtnCompBadge.TabIndex = 20;
            this.BtnCompBadge.Text = "Issue Comp Badge";
            this.BtnCompBadge.UseVisualStyleBackColor = true;
            this.BtnCompBadge.Click += new System.EventHandler(this.BtnCompBadge_Click);
            // 
            // BtnRefresh
            // 
            this.BtnRefresh.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnRefresh.Location = new System.Drawing.Point(7, 375);
            this.BtnRefresh.Name = "BtnRefresh";
            this.BtnRefresh.Size = new System.Drawing.Size(115, 27);
            this.BtnRefresh.TabIndex = 21;
            this.BtnRefresh.Text = "Refresh Badges";
            this.BtnRefresh.UseVisualStyleBackColor = true;
            this.BtnRefresh.Click += new System.EventHandler(this.BtnRefresh_Click);
            // 
            // rdoOrderNumber
            // 
            this.rdoOrderNumber.AutoSize = true;
            this.rdoOrderNumber.Checked = true;
            this.rdoOrderNumber.Font = new System.Drawing.Font("Lucida Console", 9.75F);
            this.rdoOrderNumber.Location = new System.Drawing.Point(7, 248);
            this.rdoOrderNumber.Margin = new System.Windows.Forms.Padding(2);
            this.rdoOrderNumber.Name = "rdoOrderNumber";
            this.rdoOrderNumber.Size = new System.Drawing.Size(193, 17);
            this.rdoOrderNumber.TabIndex = 23;
            this.rdoOrderNumber.TabStop = true;
            this.rdoOrderNumber.Text = "Order by Badge Number";
            this.rdoOrderNumber.UseVisualStyleBackColor = true;
            // 
            // rdoOrderName
            // 
            this.rdoOrderName.AutoSize = true;
            this.rdoOrderName.Font = new System.Drawing.Font("Lucida Console", 9.75F);
            this.rdoOrderName.Location = new System.Drawing.Point(7, 267);
            this.rdoOrderName.Margin = new System.Windows.Forms.Padding(2);
            this.rdoOrderName.Name = "rdoOrderName";
            this.rdoOrderName.Size = new System.Drawing.Size(169, 17);
            this.rdoOrderName.TabIndex = 24;
            this.rdoOrderName.Text = "Order by Last Name";
            this.rdoOrderName.UseVisualStyleBackColor = true;
            // 
            // CmbPrintOption
            // 
            this.CmbPrintOption.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CmbPrintOption.FormattingEnabled = true;
            this.CmbPrintOption.Items.AddRange(new object[] {
            "Print Both Labels",
            "Print Front Label Only",
            "Print Back Label Only"});
            this.CmbPrintOption.Location = new System.Drawing.Point(7, 222);
            this.CmbPrintOption.Name = "CmbPrintOption";
            this.CmbPrintOption.Size = new System.Drawing.Size(260, 21);
            this.CmbPrintOption.TabIndex = 25;
            // 
            // FrmBadges
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(737, 408);
            this.Controls.Add(this.CmbPrintOption);
            this.Controls.Add(this.rdoOrderName);
            this.Controls.Add(this.rdoOrderNumber);
            this.Controls.Add(this.BtnRefresh);
            this.Controls.Add(this.BtnCompBadge);
            this.Controls.Add(this.BtnEditBadge);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.TxtResumeNum);
            this.Controls.Add(this.LblStatus);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.BtnSearch);
            this.Controls.Add(this.LstResults);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.TxtBadgeNumber);
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TxtLastName);
            this.Controls.Add(this.BtnPrintSelected);
            this.Controls.Add(this.BtnPrintAll);
            this.Controls.Add(this.LstBadges);
            this.Controls.Add(this.label2);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.Name = "FrmBadges";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterParent;
            this.Text = "Print Badge Labels";
            this.FormClosing += new System.Windows.Forms.FormClosingEventHandler(this.FrmPrintBadges_FormClosing);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.ListView LstBadges;
        private System.Windows.Forms.ColumnHeader colBadgeType;
        private System.Windows.Forms.ColumnHeader colBadgeCount;
        private System.Windows.Forms.Button BtnPrintAll;
        private System.Windows.Forms.Button BtnPrintSelected;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.TextBox TxtBadgeNumber;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox TxtLastName;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.ListView LstResults;
        private System.Windows.Forms.ColumnHeader colSearchFirstName;
        private System.Windows.Forms.ColumnHeader colSearchBadgeName;
        private System.Windows.Forms.Button BtnSearch;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Label LblStatus;
        private System.Windows.Forms.ColumnHeader colSearchNum;
        private System.Windows.Forms.ColumnHeader colSearchLastName;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TxtResumeNum;
        private System.Windows.Forms.Button BtnEditBadge;
        private System.Windows.Forms.Button BtnCompBadge;
        private System.Windows.Forms.ImageList SortImages;
        private System.Windows.Forms.Button BtnRefresh;
        private System.Windows.Forms.RadioButton rdoOrderNumber;
        private System.Windows.Forms.RadioButton rdoOrderName;
        private System.Windows.Forms.ComboBox CmbPrintOption;
    }
}