<?php
if (!function_exists('renderStatusButtons')) {
    function renderStatusButtons($current_status, $order_id) {
        $buttons = [
            'Assigned' => [
                ['status' => 'Packaging', 'class' => 'primary', 'icon' => 'box', 'label' => 'Start Packaging']
            ],
            'Packaging' => [
                ['status' => 'Shipped', 'class' => 'info', 'icon' => 'truck', 'label' => 'Mark Shipped']
            ],
            'Shipped' => [
                ['status' => 'Out for Delivery', 'class' => 'warning', 'icon' => 'road', 'label' => 'Out for Delivery']
            ],
            'Out for Delivery' => [
                ['status' => 'Delivered', 'class' => 'success', 'icon' => 'check', 'label' => 'Mark Delivered'],
                ['status' => 'Failed Delivery', 'class' => 'danger', 'icon' => 'times', 'label' => 'Failed Delivery']
            ]
        ];
        
        if (!isset($buttons[$current_status])) return '';
        
        $html = '<div class="btn-group-vertical" style="width:100%">';
        foreach ($buttons[$current_status] as $btn) {
            $html .= '<button type="submit" name="status" value="'.$btn['status'].'" 
                     class="btn btn-'.$btn['class'].' btn-sm mb-1">
                     <i class="fas fa-'.$btn['icon'].'"></i> '.$btn['label'].'</button>';
        }
        $html .= '</div>';
        
        return $html;
    }
}
?>