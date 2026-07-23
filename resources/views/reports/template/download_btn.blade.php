<div id="downloadBtn" style="margin-bottom: 5px;background: #fff;">
    <style>
        .report-export-menu .dropdown-item {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
            line-height: 1.25;
        }
        .report-export-menu .dropdown-item small {
            font-size: 10px;
            line-height: 1.2;
        }
    </style>
    <div style="padding: 0 25px;">
        <div class="row">
            <div class="col-lg-12 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download
                    </button>
                    <div class="dropdown-menu dropdown-menu-right report-export-menu">
                        <div class="dropdown-item-text px-3 py-2 border-bottom" id="reportExportRowInfo" style="font-size: 11px; color: #6c757d; white-space: normal;">
                            Checking record count...
                        </div>
                        <a class="dropdown-item btnReportExport" href="javascript:void(0)" data-export="xlsx" title="Best for formatted reports in Excel">
                            <span class="d-block">Excel (XLSX)</span>
                            <small class="text-muted">Formatted layout &amp; Arabic text</small>
                        </a>
                        <a class="dropdown-item btnReportExport" href="javascript:void(0)" data-export="csv" title="Best for large exports and raw data">
                            <span class="d-block">CSV</span>
                            <small class="text-muted">Raw data, large record sets</small>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item btnReportExport" href="javascript:void(0)" data-export="pdf">
                            <span class="d-block">PDF</span>
                            <small class="text-muted">Print &amp; share</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
