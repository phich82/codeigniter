<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/../../../core/validator/FormRequest.php');

class CreateOrdersRequest extends FormRequest
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
        $rules = [
            'X-API-KEY' => 'required', // header
        ];

        if (isset($this->params['company_code'])) {
            $rules['company_code'] = 'required|max_length[6]';
        }

        if (isset($this->params['store_code'])) {
            $rules['store_code'] = 'required|max_length[3]';
        }

        $from = [RSV_SALES_VALUE, DEMAEKAN_VALUE];

        // for creating the multiple orders
        if (isset($this->params['multiple'])) {
            return array_merge($rules, []);
        }

        $validationRules = array_merge($rules, [
            'ope_id'               => 'required|in_list['.ORDER_SALES_TEXT.']|max_length[11]',
            'from'                 => 'required|in_list['.implode(",", $from).']|max_length[2]',
            'customer_code'        => 'required|max_length[255]',
            'last_name'            => 'max_length[255]',
            'last_name_kana'       => 'max_length[255]',
            'first_name'           => 'max_length[255]',
            'first_name_kana'      => 'max_length[255]',
            'customer_phone'       => 'max_length[50]|regex_match[/^(\w+)?\-[0-9\-]+$/u]',
            'order_id'             => 'required|max_length[10]',
            'payment_method'       => 'in[1,2]',
            'order_datetime'       => 'datetime[YmdHi]|max_length[12]',
            'contact_matter'       => 'max_length[200]',
            'total_price'          => 'numeric',
            'rsv_order_status'     => 'required|in[00,10,20,30]',
            'auto_print_time'      => 'numeric|max_length[3]',
            'cancel_deadline_date' => 'datetime[YmdHi]',
            'reserve_date'         => 'datetime[YmdHi]|max_length[12]',
            //'details'              => 'array',
        ]);

         // only create an order
        return array_merge($validationRules, $this->_getRulesRsvOrderStatus($validationRules));
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'customer_phone.regex_match' => 'The {field} required at least a -'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }

    /**
     * Get the 'required' rules for validating when RSV order status 10. If RSV order status is 30, not required
     *
     * @return array
     */
    private function _getRulesRsvOrderStatus($rules)
    {
        $isValid       = isset($this->params['rsv_order_status']) && $this->params['rsv_order_status'] != RSV_ORDER_STATUS_CANCELLED;
        $notEmptyArray = is_array($this->params['details']) && count($this->params['details']) > 0 ;
        # Check Update and Insert if has an array.
        if ($isValid) {
            $rules['last_name']            = 'required|'.$rules['last_name'] ;
            $rules['first_name']           = 'required|'.$rules['first_name'];
            $rules['customer_phone']       = 'required|'.$rules['customer_phone'];
            $rules['order_datetime']       = 'required|'.$rules['order_datetime'];
            $rules['total_price']          = 'required|'.$rules['total_price'];
            $rules['auto_print_time']      = 'required|'.$rules['auto_print_time'];
            $rules['cancel_deadline_date'] = 'required|'.$rules['cancel_deadline_date'];
            $rules['reserve_date']         = 'required|'.$rules['reserve_date'];
            # Check Update and Insert if has an array.
            $rules['details']                   = 'array[min[1]]';
            $rules['details.*.menu_code']       = 'required|max_length[5]';
            $rules['details.*.commodity_code']  = 'required|max_length[11]';
            $rules['details.*.menu_name']       = 'required|max_length[128]';
            $rules['details.*.menu_creation_time']     = 'required|numeric';
            $rules['details.*.menu_price']             = 'required|numeric';
            $rules['details.*.tax_code']               = 'required|max_length[10]';
            $rules['details.*.tax_status']             = 'required|max_length[2]';
            $rules['details.*.menu_p_category_status'] = 'required|max_length[8]';
            $rules['details.*.menu_category_status']   = 'required|max_length[8]';
            $rules['details.*.qty']                    = 'required|numeric';
            #Submenus
            $rules['details.*.submenues']                            = '';

            if (isset($this->params['details']) && count($this->params['details']) > 0) {
                foreach ($this->params['details'] as $k1 => $detail) {
                    if (isset($detail['submenues']) && count($detail['submenues']) > 0) {
                        foreach ($detail['submenues'] as $k2 => $submenu) {
                            $rules['details.'.$k1.'.submenues.'.$k2.'.sub_menu_code'] = 'required|max_length[5]';
                            $rules['details.'.$k1.'.submenues.'.$k2.'.sub_menu_category_status'] = 'required|max_length[8]';
                            $rules['details.'.$k1.'.submenues.'.$k2.'.sub_menu_commodity_code'] = 'required|max_length[11]';
                            $rules['details.'.$k1.'.submenues.'.$k2.'.sub_menu_name'] = 'required|max_length[128]';
                            $rules['details.'.$k1.'.submenues.'.$k2.'.sub_menu_price'] = 'required|numeric|regex_match[/^\d{1,21}(\.\d{1,3})?$/u]';
                            $rules['details.'.$k1.'.submenues.'.$k2.'.qty'] = 'required|numeric';
                        }
                    }
                }
            }
        } elseif ($notEmptyArray) {
            #Details
            $rules['details']                   = 'array[min[1]]';
            $rules['details.*.menu_code']       = 'max_length[5]';
            $rules['details.*.commodity_code']  = 'max_length[11]';
            $rules['details.*.menu_name']       = 'max_length[128]';
            $rules['details.*.menu_creation_time']     = 'numeric';
            $rules['details.*.menu_price']             = 'numeric';
            $rules['details.*.tax_code']               = 'max_length[10]';
            $rules['details.*.tax_status']             = 'max_length[2]';
            $rules['details.*.menu_p_category_status'] = 'max_length[8]';
            $rules['details.*.menu_category_status']   = 'max_length[8]';
            $rules['details.*.qty']                    = 'numeric';
            #Submenus
            $rules['details.*.submenues']                            = '';
            $rules['details.*.submenues.*.sub_menu_code']            = 'max_length[5]';
            $rules['details.*.submenues.*.sub_menu_category_status'] = 'max_length[8]';
            $rules['details.*.submenues.*.sub_menu_commodity_code']  = 'max_length[11]';
            $rules['details.*.submenues.*.sub_menu_name']            = 'max_length[128]';
            $rules['details.*.submenues.*.sub_menu_price']           = 'numeric|regex_match[/^\d{1,21}(\.\d{1,3})?$/u]';
            $rules['details.*.submenues.*.qty']                      = 'numeric';
        }
        return $rules;
    }
}
