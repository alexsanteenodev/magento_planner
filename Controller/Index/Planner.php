<?php
namespace Rebus\PlannerCart\Controller\Index;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Exception\NotFoundException;

class Planner extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $formKey;
    protected $request;
    protected $cart;
    protected $product;
    protected $productRepository;
    protected $resourceModel;



    public function __construct(Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Framework\Data\Form\FormKey $formKey,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                                \Magento\Catalog\Model\ResourceModel\Product $resourceModel
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->product = $product;
        $this->formKey = $formKey;
        $this->productRepository = $productRepository;
        $this->resourceModel = $resourceModel;
        parent::__construct($context);
    }
    public function execute(){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $post = $this->getRequest()->getParams();



            if(!empty($post['command'] && $post['command'] == 'basket')){
                foreach ($post['items'] as $product_item){
                    $params = array(
                        'qty' => $product_item['quantity']
                    );
                    $product_sku = trim(preg_replace('~\s+~s', '', $product_item['id']));

                    $productId = $this->resourceModel->getIdBySku($product_sku);

                    if(!empty($productId)){
                        $_product = $this->productRepository->get($product_sku);
                        if($_product){
                            $this->cart->addProduct($_product,$params);
                        }
                    }
                }
            }
            if($this->cart->save()){
                echo json_encode('success');
            };
        }else{
            throw new NotFoundException(__('Page not found'));
        }


    }

}
?>