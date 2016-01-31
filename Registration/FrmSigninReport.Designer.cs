namespace Registration
{
    partial class FrmSigninReport
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
            Microsoft.Reporting.WinForms.ReportDataSource reportDataSource1 = new Microsoft.Reporting.WinForms.ReportDataSource();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmSigninReport));
            this.badgesBindingSource = new System.Windows.Forms.BindingSource(this.components);
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            ((System.ComponentModel.ISupportInitialize)(this.badgesBindingSource)).BeginInit();
            this.SuspendLayout();
            // 
            // badgesBindingSource
            // 
            this.badgesBindingSource.DataSource = typeof(Registration.GeneratedBadge);
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            reportDataSource1.Name = "GeneratedBadge";
            reportDataSource1.Value = this.badgesBindingSource;
            this.RptViewer.LocalReport.DataSources.Add(reportDataSource1);
            this.RptViewer.LocalReport.ReportEmbeddedResource = "Registration.SignInReport.rdlc";
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.ShowBackButton = false;
            this.RptViewer.ShowCredentialPrompts = false;
            this.RptViewer.ShowFindControls = false;
            this.RptViewer.ShowParameterPrompts = false;
            this.RptViewer.ShowRefreshButton = false;
            this.RptViewer.ShowStopButton = false;
            this.RptViewer.Size = new System.Drawing.Size(750, 514);
            this.RptViewer.TabIndex = 1;
            // 
            // FrmSigninReport
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(96F, 96F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Dpi;
            this.ClientSize = new System.Drawing.Size(750, 514);
            this.Controls.Add(this.RptViewer);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "FrmSigninReport";
            this.Text = "Signin and Pickup Report";
            this.Load += new System.EventHandler(this.FrmSigninReport_Load);
            ((System.ComponentModel.ISupportInitialize)(this.badgesBindingSource)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;
        private System.Windows.Forms.BindingSource badgesBindingSource;

    }
}