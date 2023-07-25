<?php

/**
 * Returns the discount amount.
 * Amount * Discount%
 *
 * @param $amount
 * @param $discount
 * @return float|int
 */
function calculateDiscount($amount, $discount)
{
    return $amount * ($discount / 100);
}

/**
 * Returns the amount after discount.
 * Amount - Discount$
 *
 * @param $amount
 * @param $discount
 * @return float|int
 */
function calculatePostDiscount($amount, $discount)
{
    return $amount - calculateDiscount($amount, $discount);
}

/**
 * Returns the inclusive taxes amount.
 * PostDiscount - PostDiscount / (1 + TaxRate)
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRate
 * @return float|int
 */
function calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRate)
{
    return calculatePostDiscount($amount, $discount) - (calculatePostDiscount($amount, $discount) / (1 + ($inclusiveTaxRate / 100)));
}

/**
 * Returns the amount after discount and included taxes.
 * PostDiscount - InclusiveTaxes$
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRates
 * @return float|int
 */
function calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates)
{
    return calculatePostDiscount($amount, $discount) - calculateInclusiveTaxes($amount, $discount, $inclusiveTaxRates);
}

/**
 * Returns the amount of an inclusive tax.
 * PostDiscountLessInclTaxes * (Tax / 100)
 *
 * @param $amount
 * @param $discount
 * @param $inclusiveTaxRate
 * @param $inclusiveTaxRates
 * @return float|int
 */
function calculateInclusiveTax($amount, $discount, $inclusiveTaxRate, $inclusiveTaxRates)
{
    return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($inclusiveTaxRate / 100);
}

/**
 * Returns the exclusive tax amount.
 * PostDiscountLessInclTaxes * TaxRate
 *
 * @param $amount
 * @param $discount
 * @param $exclusiveTaxRate
 * @param $inclusiveTaxRates
 * @return float|int
 */
function checkoutExclusiveTax($amount, $discount, $exclusiveTaxRate, $inclusiveTaxRates)
{
    return calculatePostDiscountLessInclTaxes($amount, $discount, $inclusiveTaxRates) * ($exclusiveTaxRate / 100);
}

/**
 * Calculate the total, including the exclusive taxes.
 * PostDiscount + ExclusiveTax$
 *
 * @param $amount
 * @param $discount
 * @param $exclusiveTaxRates
 * @param $inclusiveTaxRates
 * @return float|int
 */
function checkoutTotal($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates)
{
    return calculatePostDiscount($amount, $discount) + checkoutExclusiveTax($amount, $discount, $exclusiveTaxRates, $inclusiveTaxRates);
}

/**
 * Get the enabled payment processors.
 *
 * @return array
 */
function paymentProcessors()
{
    $paymentProcessors = config('payment.processors');

    foreach ($paymentProcessors as $key => $value) {
        // Check if the payment processor is not enabled
        if (!config('settings.' . $key)) {
            // Remove the payment processor from the list
            unset($paymentProcessors[$key]);
        }
    }

    return $paymentProcessors;
}