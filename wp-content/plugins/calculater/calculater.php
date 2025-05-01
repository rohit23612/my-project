<?php
/**
 * Plugin Name: Loan Calculator
 * Description: A simple loan calculator with a two-column layout.
 * Version: 1.1
 * Author: Your Name
 */

function loan_calculator_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'color' => '#0073e6',
             'apply_url' => '#'
        ),
        $atts
    );
// print_r($atts);
    ob_start();
    ?>
    <style>
        .loan-calculator {
            display: flex;
            flex-wrap: wrap;
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            background: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .loan-inputs, .loan-results {
            width: 50%;
            padding: 10px;
        }
        .loan-inputs {
            text-align: left;
        }
        .loan-results {
            text-align: center;
        }
        .loan-calculator h3 {
            margin-bottom: 15px;
        }
        .loan-calculator label {
            display: block;
            margin-bottom: 5px;
        }
        .loan-calculator input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .loan-calculator button {
            width: 100%;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        .loan-results{
            background: <?php echo esc_attr($atts['color']); ?>;
        }
        .slider-container {
            margin-bottom: 15px;
        }
    </style>

    <div class="loan-calculator">
        <div class="loan-inputs">
            <h3>Loan Calculator</h3>
            <div class="slider-container">
                <label>Loan Amount: <span id="loanAmountText">$5000</span></label>
                <input type="range" id="loanAmount" min="5000" max="250000" step="5000" value="5000">
            </div>
            <div class="slider-container">
                <label>Loan Term: <span id="loanTermText">1 year</span></label>
                <input type="range" id="loanTerm" min="1" max="7" step="1" value="1">
            </div>
            <div class="slider-container">
                <label>Interest Rate: <span id="interestRateText">1%</span></label>
                <input type="range" id="interestRate" min="1" max="20" step="0.5" value="1">
            </div>
        </div>

        <div class="loan-results">
            <h4>Monthly Repayment: <span id="monthlyPayment">$0.00</span></h4>
            <a href="<?php echo esc_url($atts['apply_url']); ?>" target="_blank">
                <button>Apply Now</button>
            </a>
        </div>
    </div>

    <script>
        function calculateLoan() {
            // alert('working');
            let amount = document.getElementById('loanAmount').value;
            let years = document.getElementById('loanTerm').value;
            let rate = document.getElementById('interestRate').value;

            let monthlyRate = rate / 100 / 12;
            let months = years * 12;
            let payment = (amount * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -months));
            // console.log(payment);
            document.getElementById('monthlyPayment').innerText = "$" + payment.toFixed(2);
        }

        document.getElementById('loanAmount').addEventListener('input', function() {
            document.getElementById('loanAmountText').innerText = "$" + this.value;
            calculateLoan();
        });

        document.getElementById('loanTerm').addEventListener('input', function() {
            document.getElementById('loanTermText').innerText = this.value + " year" + (this.value > 1 ? "s" : "");
            calculateLoan();
        });

        document.getElementById('interestRate').addEventListener('input', function() {
            document.getElementById('interestRateText').innerText = this.value + "%";
            calculateLoan();
        });

        calculateLoan();
    </script>
    
    <?php
    return ob_get_clean();
}
add_shortcode('loan_calculator', 'loan_calculator_shortcode');
