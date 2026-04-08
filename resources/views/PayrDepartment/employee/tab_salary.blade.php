
<form id="repeater-form">

  <table class="repeater-table" id="repeater-table">
    <thead>
      <tr>
        <th>Select Allowance</th>
        <th>Type</th>
        <th>Value 1</th>
        <th>Value 2</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody id="repeater-body">
      <div id="formula_builder_section" class="d-none border-0 shadow-sm rounded-4 p-4 mb-4 col-md-12" style="background: #f8f9fa;">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-cpu"></i> Formula Builder</h6>
            <small class="text-muted">Build dynamic calculations for allowances</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <label class="form-label small fw-semibold text-uppercase text-primary ls-1">System Variables</label>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-white border shadow-sm var-btn hover-elevate" data-val="BASIC">
                    <span class="text-primary fw-bold">#</span> BASIC
                </button>
                <button type="button" class="btn btn-info btn-white border shadow-sm var-btn hover-elevate" data-val="WORKING_DAYS">
                    <span class="text-primary fw-bold">#</span> WORKING_DAYS
                </button>
                <button type="button" class="btn btn-white border shadow-sm var-btn hover-elevate" data-val="DAILY_RATE">
                    <span class="text-primary fw-bold">#</span> DAILY_RATE
                </button>
                <button type="button" class="btn btn-white border shadow-sm var-btn hover-elevate" data-val="OVERTIME_HOURS">
                    <span class="text-primary fw-bold">#</span> OVERTIME_HOURS
                </button>
            </div>
        </div>

        <div class="col-md-5">
            <label class="form-label small fw-semibold text-uppercase text-secondary ls-1">Mathematical Operators</label>
            <div class="d-flex flex-wrap gap-1">
                <button type="button" class="btn btn-dark btn-sm px-3 var-btn shadow-sm" data-val=" + ">+</button>
                <button type="button" class="btn btn-dark btn-sm px-3 var-btn shadow-sm" data-val=" - ">-</button>
                <button type="button" class="btn btn-dark btn-sm px-3 var-btn shadow-sm" data-val=" * ">×</button>
                <button type="button" class="btn btn-dark btn-sm px-3 var-btn shadow-sm" data-val=" / ">÷</button>
                <button type="button" class="btn btn-outline-dark btn-sm px-3 var-btn shadow-sm" data-val="(">(</button>
                <button type="button" class="btn btn-outline-dark btn-sm px-3 var-btn shadow-sm" data-val=")">)</button>
            </div>
        </div>
    </div>

    <hr class="my-3 opacity-10">

    <div class="row align-items-center">
        <div class="col-md-12">
            <div id="formula-preview-section" class="rounded-3 p-3 border-start border-primary border-4 shadow-sm" style="background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-uppercase text-muted">Live Construction Preview</span>
                    <span class="badge rounded-pill bg-light text-dark border">Syntax Check: Auto</span>
                </div>
                <div id="formula-preview" class="fs-5 font-monospace text-primary py-1" style="min-height: 1.5em; letter-spacing: 1px;">
                    <span class="text-muted small">Select a variable to begin...</span>
                </div>
            </div>
            <div class="mt-2 text-end">
                <small class="text-muted italic"><strong>Tip:</strong> Example: <code class="text-dark">(BASIC * 0.4) + 500</code></small>
            </div>
        </div>
    </div>
</div>

      
                        
      <tr class="form-row-repeater">
        <td>
          <select name="type[]" class="type-select" required>
            <option value="">Select Type</option>
            @foreach($data['allowance'] as $allowance)
            <option value="{{ $allowance->name }}" data-type="{{ $allowance->type }}">{{ $allowance->name }}</option>
            @endforeach
          </select>
        </td>
        <td><input type="text" name="field1[]" class="type_input" placeholder="Enter Value" readonly></td>
        <td>
            <select name="calculation_type[]" class="form-select calc-type-select">
                <option value="fixed">Fixed Amount</option>
                <option value="percentage">Percentage (%)</option>
                <option value="formula">Formula-Based</option>
            </select>
        </td>
        <td>
            <textarea name="formula[]" class="form-control mb-2 font-monospace formula_input" 
                      rows="1" placeholder="Value or Formula..."></textarea>
        </td>
        <td>
          
          <button type="button" class="remove-btn" onclick="removeRow(this)">Delete</button>
        </td>
      </tr>
    </tbody>
  </table>

  <div class="form-footer">
    <button type="button" class="add-btn" onclick="addRow()">+ Add New Row</button>
    <hr>
    <button type="submit" class="submit-btn">Update All Records</button>
    <div id="form-feedback"></div>
  </div>
</form>

<style>
  .repeater-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .repeater-table th { text-align: left; padding: 10px; background: #eee; border: 1px solid #ddd; }
  .repeater-table td { padding: 10px; border: 1px solid #ddd; }
  
  input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
  
  .remove-btn { background: #dc3545; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 4px; }
  .add-btn { background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; margin: 10px 0; border-radius: 4px; }
  .submit-btn { background: #007bff; color: white; border: none; padding: 12px 25px; cursor: pointer; border-radius: 4px; }
  
  .error-border { border: 2px solid #dc3545 !important; background-color: #fff8f8; }
  #form-feedback { margin-top: 10px; font-weight: bold; }

  /* Force the table to respect defined widths */
.repeater-table {
  table-layout: fixed; /* Crucial for controlling widths */
  width: 100%;
}

/* Define widths for each column */
.repeater-table th:nth-child(1), .repeater-table td:nth-child(1) { width: 30%; } /* Select Allowance */
.repeater-table th:nth-child(2), .repeater-table td:nth-child(2) { width: 20%; } /* Type */
.repeater-table th:nth-child(3), .repeater-table td:nth-child(3) { width: 15%; } /* Value 1 */
.repeater-table th:nth-child(4), .repeater-table td:nth-child(4) { width: 15%; } /* Value 2 */
.repeater-table th:nth-child(5), .repeater-table td:nth-child(5) { width: 20%; } /* Action */

/* Make sure inputs don't overflow the fixed columns */
.repeater-table input, .repeater-table select {
  width: 100%;
  box-sizing: border-box; 
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

  $(document).ready(function() {

  // Function to update the preview based on the focused or last active row
    function updatePreview(text) {
        if (text.trim() === "") {
            $('#formula-preview').text("---");
        } else {
            $('#formula-preview').text(text);
        }
    }
    // A. Update preview when typing manually
    $(document).on('input', '.formula_input', function() {
        updatePreview($(this).val());
    });
    
    // 1. Insert Variable/Operator into the LATEST row's textarea
    $(document).on('click', '.var-btn', function() {
        const valueToInsert = $(this).data('val');
        
        // Find the formula_input in the very last row
        const $lastRow = $('.form-row-repeater').last();
        const $calcType = $lastRow.find('.calc-type-select').val();
        
        // Only allow variable insertion if mode is 'formula'
        if($calcType !== 'formula') {
            alert("Please change Calculation Type to 'Formula-Based' to use variables.");
            return;
        }

        const $textarea = $lastRow.find('.formula_input');
        const domElement = $textarea[0];
        const start = domElement.selectionStart;
        const end = domElement.selectionEnd;
        const text = $textarea.val();

        $textarea.val(text.substring(0, start) + valueToInsert + text.substring(end));
        $textarea.focus();
        domElement.setSelectionRange(start + valueToInsert.length, start + valueToInsert.length);

        // After inserting the text, update the preview
        const currentText = $('.form-row-repeater').last().find('.formula_input').val();
        updatePreview(currentText);
    });

    // 2. Dynamic Input Validation based on Calculation Type
    $(document).on('change', '.calc-type-select', function() {
            const row = $(this).closest('tr');
            const type = $(this).val();
            const $textarea = row.find('.formula_input');
            const $builder = $('#formula_builder_section');

            if (type === 'formula') {
                $builder.removeClass('d-none').hide().slideDown();
                $textarea.attr('placeholder', 'Click variables to build...');
            } else {
                // If it's fixed or percentage, ensure it's a number
                $textarea.attr('placeholder', 'Enter numeric value (e.g. 10.5)');
                // Optional: check if formula builder is needed by other rows
                if ($('.calc-type-select').filter((i, el) => $(el).val() === 'formula').length === 0) {
                    $builder.slideUp();
                }
            }
        });
    });

    // 3. Prevent adding row if textarea is empty
    function addRow() {
        const tbody = document.getElementById('repeater-body');
        const lastRow = $('.form-row-repeater').last();
        const lastTextarea = lastRow.find('.formula_input').val().trim();
        const lastCalcType = lastRow.find('.calc-type-select').val();

        // VALIDATION: Prevent empty rows
        if (lastTextarea === "") {
            alert("Please enter a value or formula in the current row before adding a new one.");
            lastRow.find('.formula_input').addClass('error-border').focus();
            return;
        }

        // VALIDATION: If fixed/percentage, ensure it's a valid number
        if (lastCalcType !== 'formula' && isNaN(lastTextarea)) {
            alert("For Fixed/Percentage, please enter a valid decimal or integer.");
            lastRow.find('.formula_input').addClass('error-border').focus();
            return;
        }

        lastRow.find('.formula_input').removeClass('error-border');

        // CLONING LOGIC
        const firstRow = document.querySelector('.form-row-repeater');
        const newRow = firstRow.cloneNode(true);
        
        // Clear values
        $(newRow).find('input, textarea').val('');
        $(newRow).find('select').prop('selectedIndex', 0).removeClass('error-border');
        $(newRow).find('.formula_input').removeClass('error-border');

        tbody.appendChild(newRow);
        updatePreview("");
    }

    function removeRow(btn) {
        if ($('.form-row-repeater').length > 1) {
            $(btn).closest('tr').remove();
        } else {
            alert("At least one row is required.");
        }
    }

  
    $(document).on('change', '.type-select', function() {
        // 1. Get the selected option's data attribute
        let selectedType = $(this).find(':selected').data('type');

        console.log("Data ready for submission:",selectedType);

        // 2. Find the input field in the same row (td) or next td
        // We go up to the parent <tr> then find the input with class .type_input
        $(this).closest('tr').find('.type_input').val(selectedType);
    });

      document.getElementById('repeater-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const selects = document.querySelectorAll('.type-select');
        const feedback = document.getElementById('form-feedback');
        
        const seen = new Set();
        let hasDup = false;
        
        selects.forEach(s => {
          s.classList.remove('error-border');
          const val = s.value.trim();
          if (val) {
            if (seen.has(val)) {
              hasDup = true;
              s.classList.add('error-border');
            }
            seen.add(val);
          }
        });

        if (hasDup) {
          feedback.style.color = 'red';
          feedback.innerHTML = 'Error: Duplicate allowance types selected!';
          return;
        }

        feedback.style.color = 'blue';
        feedback.innerHTML = 'Processing update...';

        // To send via AJAX:
        const formData = new FormData(this);
        console.log("Data ready for submission:", Object.fromEntries(formData.entries()));
        
        // Example Fetch:
        // fetch('/update-route', { method: 'POST', body: formData })
        // .then(res => res.json()).then(data => { ... success logic ... });
      });
</script>