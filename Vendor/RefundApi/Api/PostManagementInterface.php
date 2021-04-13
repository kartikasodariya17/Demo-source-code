<?php 
namespace Vendor\RefundApi\Api;
 
 
interface PostManagementInterface
{


    /**
     * GET for Post api
     * @param \Vendor\RefundApi\Api\Data\PostManagementInterface[] $highlightData
     * @return string
     */
    
    public function getPost($highlightData);
}