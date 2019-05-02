<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../../../core/validator/FormRequest.php');

class GetOrdersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // create the rules for updating the orders if it is required
        $rulesForUpdate = [
            'update_list' => 'array'
        ];
        if (is_array($this->params['update_list']) && count($this->params['update_list']) > 0) {
            $printStatuses     = [PRINT_STATUS_NO, PRINT_STATUS_SUCCESS, PRINT_STATUS_FAIL];
            $sendToPosStatuses = [NO_SENT_TO, SENT_TO_POS, SENT_TO_POS_FAILED];
            $orderStatuses     = [
                ORDER_STATUS_RESERVED,
                ORDER_STATUS_CONFIRMED,
                ORDER_STATUS_COOKING,
                ORDER_STATUS_COMPLETED,
                ORDER_STATUS_UNRECEIVED,
                ORDER_STATUS_CANCELLED,
            ];

            $rulesForUpdate = [
                'update_list'                         => 'array[min[1]]',
                'update_list.*.order_id'              => 'required|max_length[10]|exist_by[trn_self_orders:order_id:company_code=>pos_company_code:store_code=>pos_store_code]',
                'update_list.*.print_status'          => 'in_list['.implode(",", $printStatuses).']',
                'update_list.*.order_status'          => 'required|in_list['.implode(",", $orderStatuses).']',
                'update_list.*.table_number'          => 'max_length[9]',
                'update_list.*.sent_to_pos'           => 'in['.implode(",", $sendToPosStatuses).']',
            ];
        }
        return array_merge([
            'X-API-KEY'       => 'required',               // header
            'company_code'    => 'required|max_length[6]', // header
            'store_code'      => 'required|max_length[3]', // header
            'order_receive'   => 'required|datetime[Ymd]',
            'order_limit'     => 'integer',
            'page'            => 'integer',
        ], $rulesForUpdate, $this->_getRulesVoucher());
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'business_hour.in' => 'The {field} must be in list [true, false].'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'business_hour' => 'Business Hour'
        ];
    }

    /**
     * Get the 'required' rules for validating the vouchers when the order status is COOKING
     *
     * @return array
     */
    private function _getRulesVoucher()
    {
        $rules = [];
        if (is_array($this->params['update_list']) && count($this->params['update_list']) > 0) {
            foreach ($this->params['update_list'] as $k => $row) {
                $key = 'update_list.'.$k.'.voucher_id';
                $rules[$key] = 'max_length[128]';
                if (isset($row['order_status']) && $row['order_status'] == ORDER_STATUS_COOKING) {
                    $rules[$key] = 'required|'.$rules[$key];
                }
            }
        }
        return $rules;
    }

}
