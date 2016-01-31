namespace Registration
{
    partial class FrmCompBadge
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmCompBadge));
            this.label6 = new System.Windows.Forms.Label();
            this.TxtLastName = new System.Windows.Forms.TextBox();
            this.LstPeople = new System.Windows.Forms.ListView();
            this.colFirstName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colLastName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colEmail = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.colBadgeName = ((System.Windows.Forms.ColumnHeader)(new System.Windows.Forms.ColumnHeader()));
            this.BtnAddPerson = new System.Windows.Forms.Button();
            this.BtnSearch = new System.Windows.Forms.Button();
            this.label5 = new System.Windows.Forms.Label();
            this.TxtRecipientName = new System.Windows.Forms.TextBox();
            this.label4 = new System.Windows.Forms.Label();
            this.TxtBadgeName = new System.Windows.Forms.TextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.BtnIssue = new System.Windows.Forms.Button();
            this.BtnCancel = new System.Windows.Forms.Button();
            this.label1 = new System.Windows.Forms.Label();
            this.TxtDepartment = new System.Windows.Forms.TextBox();
            this.SuspendLayout();
            // 
            // label6
            // 
            this.label6.AutoSize = true;
            this.label6.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label6.Location = new System.Drawing.Point(22, 62);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(87, 13);
            this.label6.TabIndex = 126;
            this.label6.Text = "Last Name:";
            // 
            // TxtLastName
            // 
            this.TxtLastName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtLastName.Location = new System.Drawing.Point(115, 59);
            this.TxtLastName.Name = "TxtLastName";
            this.TxtLastName.Size = new System.Drawing.Size(204, 20);
            this.TxtLastName.TabIndex = 125;
            // 
            // LstPeople
            // 
            this.LstPeople.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.LstPeople.Columns.AddRange(new System.Windows.Forms.ColumnHeader[] {
            this.colFirstName,
            this.colLastName,
            this.colEmail,
            this.colBadgeName});
            this.LstPeople.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.LstPeople.FullRowSelect = true;
            this.LstPeople.GridLines = true;
            this.LstPeople.HideSelection = false;
            this.LstPeople.Location = new System.Drawing.Point(22, 87);
            this.LstPeople.MultiSelect = false;
            this.LstPeople.Name = "LstPeople";
            this.LstPeople.Size = new System.Drawing.Size(667, 222);
            this.LstPeople.TabIndex = 124;
            this.LstPeople.UseCompatibleStateImageBehavior = false;
            this.LstPeople.View = System.Windows.Forms.View.Details;
            this.LstPeople.ColumnClick += new System.Windows.Forms.ColumnClickEventHandler(this.LstPeople_ColumnClick);
            this.LstPeople.SelectedIndexChanged += new System.EventHandler(this.LstPeople_SelectedIndexChanged);
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
            // colEmail
            // 
            this.colEmail.Text = "Email Address";
            this.colEmail.Width = 184;
            // 
            // colBadgeName
            // 
            this.colBadgeName.Text = "Badge Name";
            this.colBadgeName.Width = 151;
            // 
            // BtnAddPerson
            // 
            this.BtnAddPerson.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnAddPerson.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnAddPerson.Location = new System.Drawing.Point(562, 54);
            this.BtnAddPerson.Name = "BtnAddPerson";
            this.BtnAddPerson.Size = new System.Drawing.Size(127, 27);
            this.BtnAddPerson.TabIndex = 123;
            this.BtnAddPerson.Text = "Add Person";
            this.BtnAddPerson.UseVisualStyleBackColor = true;
            this.BtnAddPerson.Click += new System.EventHandler(this.BtnAddPerson_Click);
            // 
            // BtnSearch
            // 
            this.BtnSearch.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnSearch.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnSearch.Location = new System.Drawing.Point(430, 54);
            this.BtnSearch.Name = "BtnSearch";
            this.BtnSearch.Size = new System.Drawing.Size(126, 27);
            this.BtnSearch.TabIndex = 122;
            this.BtnSearch.Text = "Search";
            this.BtnSearch.UseVisualStyleBackColor = true;
            this.BtnSearch.Click += new System.EventHandler(this.BtnSearch_Click);
            // 
            // label5
            // 
            this.label5.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(22, 318);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(87, 13);
            this.label5.TabIndex = 120;
            this.label5.Text = "Recipient:";
            // 
            // TxtRecipientName
            // 
            this.TxtRecipientName.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.TxtRecipientName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtRecipientName.Location = new System.Drawing.Point(115, 315);
            this.TxtRecipientName.Name = "TxtRecipientName";
            this.TxtRecipientName.ReadOnly = true;
            this.TxtRecipientName.Size = new System.Drawing.Size(247, 20);
            this.TxtRecipientName.TabIndex = 121;
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(368, 318);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(95, 13);
            this.label4.TabIndex = 118;
            this.label4.Text = "Badge Name:";
            // 
            // TxtBadgeName
            // 
            this.TxtBadgeName.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.TxtBadgeName.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtBadgeName.Location = new System.Drawing.Point(469, 315);
            this.TxtBadgeName.Name = "TxtBadgeName";
            this.TxtBadgeName.Size = new System.Drawing.Size(221, 20);
            this.TxtBadgeName.TabIndex = 119;
            // 
            // label3
            // 
            this.label3.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(12, 9);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(678, 42);
            this.label3.TabIndex = 117;
            this.label3.Text = resources.GetString("label3.Text");
            // 
            // BtnIssue
            // 
            this.BtnIssue.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnIssue.Enabled = false;
            this.BtnIssue.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnIssue.Location = new System.Drawing.Point(564, 348);
            this.BtnIssue.Name = "BtnIssue";
            this.BtnIssue.Size = new System.Drawing.Size(127, 27);
            this.BtnIssue.TabIndex = 128;
            this.BtnIssue.Text = "Issue Badge";
            this.BtnIssue.UseVisualStyleBackColor = true;
            this.BtnIssue.Click += new System.EventHandler(this.BtnIssue_Click);
            // 
            // BtnCancel
            // 
            this.BtnCancel.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Right)));
            this.BtnCancel.Font = new System.Drawing.Font("Lucida Sans", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.BtnCancel.Location = new System.Drawing.Point(431, 348);
            this.BtnCancel.Name = "BtnCancel";
            this.BtnCancel.Size = new System.Drawing.Size(127, 27);
            this.BtnCancel.TabIndex = 127;
            this.BtnCancel.Text = "Cancel Issuance";
            this.BtnCancel.UseVisualStyleBackColor = true;
            this.BtnCancel.Click += new System.EventHandler(this.BtnCancel_Click);
            // 
            // label1
            // 
            this.label1.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.label1.AutoSize = true;
            this.label1.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(22, 347);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(159, 13);
            this.label1.TabIndex = 129;
            this.label1.Text = "Department to Bill:";
            // 
            // TxtDepartment
            // 
            this.TxtDepartment.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Bottom | System.Windows.Forms.AnchorStyles.Left)));
            this.TxtDepartment.Font = new System.Drawing.Font("Lucida Console", 9.75F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.TxtDepartment.Location = new System.Drawing.Point(187, 344);
            this.TxtDepartment.Name = "TxtDepartment";
            this.TxtDepartment.Size = new System.Drawing.Size(175, 20);
            this.TxtDepartment.TabIndex = 130;
            this.TxtDepartment.TextChanged += new System.EventHandler(this.TxtDepartment_TextChanged);
            // 
            // FrmCompBadge
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(703, 387);
            this.ControlBox = false;
            this.Controls.Add(this.label1);
            this.Controls.Add(this.TxtDepartment);
            this.Controls.Add(this.BtnIssue);
            this.Controls.Add(this.BtnCancel);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.TxtLastName);
            this.Controls.Add(this.LstPeople);
            this.Controls.Add(this.BtnAddPerson);
            this.Controls.Add(this.BtnSearch);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.TxtRecipientName);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.TxtBadgeName);
            this.Controls.Add(this.label3);
            this.Name = "FrmCompBadge";
            this.ShowInTaskbar = false;
            this.Text = "Issue Complimentary Badge";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.TextBox TxtLastName;
        private System.Windows.Forms.ListView LstPeople;
        private System.Windows.Forms.ColumnHeader colFirstName;
        private System.Windows.Forms.ColumnHeader colLastName;
        private System.Windows.Forms.ColumnHeader colEmail;
        private System.Windows.Forms.ColumnHeader colBadgeName;
        private System.Windows.Forms.Button BtnAddPerson;
        private System.Windows.Forms.Button BtnSearch;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TxtRecipientName;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.TextBox TxtBadgeName;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.Button BtnIssue;
        private System.Windows.Forms.Button BtnCancel;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.TextBox TxtDepartment;
    }
}