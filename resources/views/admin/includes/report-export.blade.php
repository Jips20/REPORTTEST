<script>
    const PHARMACY_INFO = {
        name: @json(AppSettings::get('app_name', config('app.name'))),
        branch: @json(AppSettings::get('pharmacy_branch', '')),
        address: @json(AppSettings::get('pharmacy_address', '')),
        contact: @json(AppSettings::get('pharmacy_contact', '')),
        logoUrl: @json(AppSettings::get('logo') ? asset('storage/'.AppSettings::get('logo')) : null),
        preparedBy: @json(optional(auth()->user())->name),
    };

    const REPORT_COLOR = '#0f766e';      // brand color (change to match your logo)
    const REPORT_COLOR_LIGHT = '#f0fdfa';
    const CURRENCY = '₱';                // if the symbol ever fails to render, use 'PHP '

    // ---------- helpers ----------
    let PHARMACY_LOGO_BASE64 = null;
    if (PHARMACY_INFO.logoUrl) {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = function () {
            const c = document.createElement('canvas');
            c.width = img.width; c.height = img.height;
            c.getContext('2d').drawImage(img, 0, 0);
            try { PHARMACY_LOGO_BASE64 = c.toDataURL('image/png'); } catch (e) { PHARMACY_LOGO_BASE64 = null; }
        };
        img.src = PHARMACY_INFO.logoUrl;
    }

    const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    const parseNum = s => parseFloat(String(s).replace(/[^0-9.\-]/g, '')) || 0;
    const fmtMoney = n => CURRENCY + ' ' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtNumber = n => n.toLocaleString('en-PH');
    const fmtNow = () => new Date().toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
    const looksNumeric = t => /^\s*[₱$€£]?\s*-?[\d,]+(\.\d+)?\s*%?\s*$/.test(t);
    const cellText = c => (c && typeof c === 'object') ? (c.text ?? '') : (c ?? '');

    /**
     * Letterhead: logo + pharmacy info on the left, report info on the right.
     */
    function buildPdfReportHeader(reportTitle, period) {
        const left = [
            { text: PHARMACY_INFO.name || 'Pharmacy', style: 'pharmacyName' },
        ];
        if (PHARMACY_INFO.branch)  left.push({ text: PHARMACY_INFO.branch,  style: 'pharmacyBranch' });
        if (PHARMACY_INFO.address) left.push({ text: PHARMACY_INFO.address, style: 'pharmacyMeta' });
        if (PHARMACY_INFO.contact) left.push({ text: 'Tel: ' + PHARMACY_INFO.contact, style: 'pharmacyMeta' });

        const right = [
            { text: reportTitle.toUpperCase(), style: 'reportTitle', alignment: 'right' },
            period ? { text: period, style: 'pharmacyMeta', alignment: 'right', margin: [0, 3, 0, 0] } : null,
            { text: 'Generated: ' + fmtNow(), style: 'pharmacyMeta', alignment: 'right', margin: [0, 1, 0, 0] },
        ].filter(Boolean);

        const columns = [];
        if (PHARMACY_LOGO_BASE64) columns.push({ image: PHARMACY_LOGO_BASE64, width: 52, margin: [0, 0, 12, 0] });
        columns.push({ width: '*', stack: left });
        columns.push({ width: 'auto', stack: right });

        return [
            { columns: columns, columnGap: 6 },
            // thick brand line + thin grey line
            { canvas: [
                { type: 'rect', x: 0, y: 0, w: 515, h: 3, color: REPORT_COLOR },
            ], margin: [0, 10, 0, 14] },
        ];
    }

    /**
     * Summary cards (records / totals) shown above the table.
     */
    function buildSummaryBlock(items) {
        if (!items.length) return null;
        return {
            table: {
                widths: items.map(() => '*'),
                body: [[
                    ...items.map(it => ({
                        stack: [
                            { text: it.label.toUpperCase(), fontSize: 7.5, color: '#6b7280', bold: true },
                            { text: it.value, fontSize: 13, bold: true, color: REPORT_COLOR, margin: [0, 2, 0, 0] },
                        ],
                        margin: [8, 6, 8, 6],
                    })),
                ]],
            },
            layout: {
                fillColor: () => REPORT_COLOR_LIGHT,
                hLineWidth: () => 0,
                vLineWidth: (i) => (i === 0 ? 0 : 0),
            },
            margin: [0, 0, 0, 14],
        };
    }

    /**
     * Signature block at the bottom of the report.
     */
    function buildSignatureBlock() {
        const line = { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 150, y2: 0, lineWidth: 0.7, lineColor: '#9ca3af' }] };
        return {
            unbreakable: true,
            margin: [0, 40, 0, 0],
            columns: [
                { width: 170, stack: [
                    { text: PHARMACY_INFO.preparedBy || ' ', fontSize: 9, margin: [0, 0, 0, 2] },
                    line,
                    { text: 'Prepared by', style: 'pharmacyMeta', margin: [0, 3, 0, 0] },
                ]},
                { width: '*', text: '' },
                { width: 170, stack: [
                    { text: ' ', fontSize: 9, margin: [0, 0, 0, 2] },
                    line,
                    { text: 'Reviewed / Approved by', style: 'pharmacyMeta', margin: [0, 3, 0, 0] },
                ]},
            ],
        };
    }

    /**
     * DataTables Buttons "customize" callback for pdfHtml5.
     *
     * options = {
     *   totalColumns: [{ index: 1, type: 'number' }, { index: 2, type: 'currency' }],
     *   widths: ['*', 60, 90, 90],     // optional, otherwise first col '*' and the rest 'auto'
     * }
     */
    function customizePdfReport(doc, reportTitle, period, options) {
        options = options || {};
        doc.pageSize = 'A4';
        doc.pageMargins = [40, 40, 40, 55];
        doc.defaultStyle = Object.assign({}, doc.defaultStyle, { fontSize: 9, color: '#1f2937' });
        doc.styles = Object.assign({}, doc.styles, {
            pharmacyName:   { fontSize: 16, bold: true, color: REPORT_COLOR },
            pharmacyBranch: { fontSize: 10, bold: true, color: '#374151', margin: [0, 1, 0, 0] },
            pharmacyMeta:   { fontSize: 8.5, color: '#6b7280', margin: [0, 1, 0, 0] },
            reportTitle:    { fontSize: 14, bold: true, color: '#111827' },
            tableHeader:    { bold: true, fontSize: 9, color: '#ffffff', fillColor: REPORT_COLOR },
            tableBodyEven:  {},
            tableBodyOdd:   {},
        });

        // Grab ONLY the generated table (this drops DataTables' default title line)
        const table = doc.content.find(c => c && c.table);
        if (!table) return;

        const body = table.table.body;
        const colCount = body[0].length;

        // 1) Full-width columns
        table.table.widths = options.widths ||
            body[0].map((_, i) => (i === 0 ? '*' : 'auto'));
        table.table.headerRows = 1;
        table.table.dontBreakRows = true;

        // 2) Alignment: text left, numbers right (decided from the body rows)
        const numericCols = new Set();
        for (let c = 0; c < colCount; c++) {
            const rows = body.slice(1);
            if (rows.length && rows.every(r => looksNumeric(cellText(r[c])))) numericCols.add(c);
        }
        body.forEach((row, r) => {
            row.forEach((cell, c) => {
                if (typeof cell !== 'object') cell = row[c] = { text: String(cell) };
                cell.alignment = numericCols.has(c) ? 'right' : 'left';
                if (r === 0) cell.margin = [4, 3, 4, 3];
            });
        });

        // 3) Totals row + summary cards
        const summary = [{ label: 'Total Records', value: fmtNumber(body.length - 1) }];
        if (options.totalColumns && body.length > 1) {
            const totalRow = Array.from({ length: colCount }, (_, c) => ({
                text: '', bold: true, alignment: numericCols.has(c) ? 'right' : 'left',
                fillColor: REPORT_COLOR_LIGHT,
            }));
            totalRow[0].text = 'TOTAL';
            options.totalColumns.forEach(tc => {
                const sum = body.slice(1).reduce((s, r) => s + parseNum(cellText(r[tc.index])), 0);
                const txt = tc.type === 'currency' ? fmtMoney(sum) : fmtNumber(sum);
                totalRow[tc.index].text = txt;
                summary.push({ label: 'Total ' + cellText(body[0][tc.index]), value: txt });
            });
            body.push(totalRow);
        }

        // 4) Table look: header fill, zebra stripes, hairlines
        const lastIdx = body.length - 1;
        const hasTotal = !!(options.totalColumns && body.length > 2);
        table.layout = {
            hLineWidth: (i, node) => (i === 0 || i === node.table.body.length) ? 0.8 : (hasTotal && i === lastIdx ? 1 : 0.4),
            vLineWidth: () => 0,
            hLineColor: (i) => (i === 0 ? REPORT_COLOR : '#e5e7eb'),
            fillColor: (i) => (i === 0 ? REPORT_COLOR : (i % 2 === 0 ? '#f9fafb' : null)),
            paddingLeft: () => 6,
            paddingRight: () => 6,
            paddingTop: () => 5,
            paddingBottom: () => 5,
        };

        // 5) Assemble the final document
        doc.content = [].concat(
            buildPdfReportHeader(reportTitle, period),
            buildSummaryBlock(summary) || [],
            [table],
            [buildSignatureBlock()]
        );

        // 6) Footer with line, system note and page numbers
        doc.footer = function (currentPage, pageCount) {
            return {
                margin: [40, 0, 40, 0],
                stack: [
                    { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 515, y2: 0, lineWidth: 0.5, lineColor: '#d1d5db' }], margin: [0, 0, 0, 5] },
                    { columns: [
                        { text: (PHARMACY_INFO.name || '') + (PHARMACY_INFO.branch ? ' – ' + PHARMACY_INFO.branch : ''), style: 'pharmacyMeta' },
                        { text: 'Confidential – for internal use only', style: 'pharmacyMeta', alignment: 'center' },
                        { text: 'Page ' + currentPage + ' of ' + pageCount, style: 'pharmacyMeta', alignment: 'right' },
                    ]},
                ],
            };
        };
    }

    /**
     * DataTables Buttons "customize" callback for print.
     */
    function customizePrintReport(win, reportTitle, period) {
        const doc = win.document;
        const logo = PHARMACY_LOGO_BASE64 || PHARMACY_INFO.logoUrl;

        // remove DataTables' default <h1> title
        const h1 = doc.body.querySelector('h1');
        if (h1) h1.remove();

        const header = doc.createElement('div');
        header.innerHTML = `
            <div class="rp-head">
                ${logo ? `<img class="rp-logo" src="${esc(logo)}">` : ''}
                <div class="rp-info">
                    <div class="rp-name">${esc(PHARMACY_INFO.name || 'Pharmacy')}</div>
                    ${PHARMACY_INFO.branch ? `<div class="rp-branch">${esc(PHARMACY_INFO.branch)}</div>` : ''}
                    ${PHARMACY_INFO.address ? `<div class="rp-meta">${esc(PHARMACY_INFO.address)}</div>` : ''}
                    ${PHARMACY_INFO.contact ? `<div class="rp-meta">Tel: ${esc(PHARMACY_INFO.contact)}</div>` : ''}
                </div>
                <div class="rp-report">
                    <div class="rp-title">${esc(reportTitle).toUpperCase()}</div>
                    ${period ? `<div class="rp-meta">${esc(period)}</div>` : ''}
                    <div class="rp-meta">Generated: ${esc(fmtNow())}</div>
                </div>
            </div>
            <div class="rp-bar"></div>`;
        doc.body.insertBefore(header, doc.body.firstChild);

        const footer = doc.createElement('div');
        footer.innerHTML = `
            <div class="rp-sign">
                <div><div class="rp-line"></div>Prepared by${PHARMACY_INFO.preparedBy ? ': ' + esc(PHARMACY_INFO.preparedBy) : ''}</div>
                <div><div class="rp-line"></div>Reviewed / Approved by</div>
            </div>
            <div class="rp-foot">Confidential – for internal use only</div>`;
        doc.body.appendChild(footer);

        const style = doc.createElement('style');
        style.innerHTML = `
            @page { size: A4; margin: 14mm; }
            body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; margin: 0; }
            .rp-head { display: flex; align-items: center; gap: 14px; }
            .rp-logo { width: 60px; height: auto; }
            .rp-info { flex: 1; }
            .rp-report { text-align: right; }
            .rp-name { font-size: 18px; font-weight: 700; color: ${REPORT_COLOR}; }
            .rp-branch { font-size: 12px; font-weight: 700; color: #374151; }
            .rp-title { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 3px; }
            .rp-meta { font-size: 11px; color: #6b7280; margin-top: 1px; }
            .rp-bar { height: 3px; background: ${REPORT_COLOR}; margin: 12px 0 16px; }
            table { border-collapse: collapse; width: 100%; }
            table th { background: ${REPORT_COLOR}; color: #fff; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; padding: 8px 10px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table td { font-size: 12px; padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
            table tbody tr:nth-child(even) td { background: #f9fafb; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tr { page-break-inside: avoid; }
            .rp-sign { display: flex; justify-content: space-between; margin-top: 50px; font-size: 11px; color: #6b7280; }
            .rp-sign > div { width: 190px; }
            .rp-line { border-top: 1px solid #9ca3af; margin-bottom: 4px; margin-top: 28px; }
            .rp-foot { text-align: center; font-size: 10px; color: #9ca3af; margin-top: 30px; }
        `;
        doc.head.appendChild(style);

        // right-align numeric columns
        doc.querySelectorAll('table tbody tr').forEach(tr => {
            tr.querySelectorAll('td').forEach(td => {
                if (looksNumeric(td.textContent)) td.style.textAlign = 'right';
            });
        });
        const ths = doc.querySelectorAll('table thead th');
        const firstRow = doc.querySelector('table tbody tr');
        if (firstRow) firstRow.querySelectorAll('td').forEach((td, i) => {
            if (looksNumeric(td.textContent) && ths[i]) ths[i].style.textAlign = 'right';
        });
    }
</script>