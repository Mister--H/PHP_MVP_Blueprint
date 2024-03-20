<?php
namespace App\Controllers;

use App\Models\Database;
use App\Models\WooModel;
use App\Models\CredentialModel;
use App\Models\SettingModel;
use App\Models\ContentModel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Client as GuzzleClient; // Alias GuzzleHttp\Client to avoid name conflict
use Symfony\Component\DomCrawler\Crawler;
use Automattic\WooCommerce\Client; // Correctly import the WooCommerce Client
use League\CommonMark\CommonMarkConverter;

class WooController {
    private $WooModel;
    private $CredentialModel;
    private $SettingModel;
    private $ContentModel;
    private $logger;
    private $userId;
    
    public function __construct() {
        $database = new Database();
        $this->WooModel = new WooModel($database);
        $this->CredentialModel = new CredentialModel($database);
        $this->SettingModel = new SettingModel($database);
        $this->ContentModel = new ContentModel($database);
        $this->userId = $_SESSION['user_id']['id'] ?? null;
        $this->logger = new Logger('WooController');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/WooController.log', Logger::DEBUG));
    }

    public function index() {
        renderView('dashboard/woocommerce/woo');

    }

    public function showaddProduct($message = ''){
        $credentials = $this->CredentialModel->getCredentials($this->userId);
        $data = ['credentials'=> $credentials, 'message'=>$message];
        renderView('dashboard/woocommerce/addProduct', $data);
    }   

    public function storeProduct(){
        $store = sanitizeInput($_POST['store_id'] ?? '');
        $productUrl = sanitizeInput($_POST['product_url'] ?? '');
        $haveContent = isset($_POST['have_content']);
        $needApproval =  isset($_POST['need_approval']);
        $sku = sanitizeInput($_POST['sku'] ?? '');
        $brand = sanitizeInput($_POST['brand'] ?? '');
        $price = sanitizeInput($_POST['price'] ?? '');
        
        $settings = $this->SettingModel->getWooSettings($store, $this->userId);
        $client = new GuzzleClient(['verify' => false]);

        $woocommerce = $this->initializeWooCommerce($settings);
        $html = $this->fetchProductHtml($client, $productUrl);
        $crawler = new Crawler($html);
        $productData = $this->extractProductData($crawler, $settings, $brand, $woocommerce, $productUrl, $sku, $price); 
        $response = $this->publishProductToWooCommerce($productData, $woocommerce);   

        $this->showaddProduct($response);
       
    }

    public function initializeWooCommerce($settings){
         // WooCommerce API credentials
        $consumer_key = decryptCredential($settings['consumer_key'], getenv('SECRET_KEY'));
        $consumer_secret = decryptCredential($settings['consumer_secret'], getenv('SECRET_KEY'));
        $woocommerce_url = $settings['store_url'];
        // Initialize WooCommerce REST API client
        return $woocommerce = new Client(
            $woocommerce_url,
            $consumer_key,
            $consumer_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3'
            ]
        );
    }

    private function fetchProductHtml($client, $productUrl) {
        $response = $client->request('GET', $productUrl);
        return (string) $response->getBody();
    }

    public function extractProductData($crawler, $settings, $brand, $woocommerce, $productUrl, $sku, $price){
        try {
        $category = $crawler->filter($settings['category'])->text();
        switch ($category) {
            case 'مینی فرز ( مینی سنگ )':
                $category = 'مینی فرز';
                break;
            case 'کارواش صنعتی':
                $category = 'کارواش';
                break;
            case 'مینی فرز دیمر دار':
                $category = 'مینی فرز';
                break;
            case 'فرز انگشتی و مینیاتوری':
                $category = 'فرز انگشتی';
                break;
            case 'مینی فرز شارژی':
                $category = 'مینی فرز';
                break;
            case 'مینی فرز دسته بلند':
                $category = 'مینی فرز';
                break;
            case 'فرز‌ آهنگری':
                $category = 'فرز آهنگری';
                break;
            case 'سنگ فرز':
                $category = 'فرز سنگ بری';
                break;
            case 'دستگاه پولیش':
                $category = 'پولیش';
                break;
            case 'دستگاه پولیش اوربیتال':
                $category = 'پولیش';
                break;
            case 'فارسی بر کشویی':
                $category = 'اره فارسی بر';
                break;
            case 'بتن کن و چکش تخریب':
                $category = 'بتن کن';
                break;
            case 'اور فرز مشتی':
                $category = 'اور فرز نجاری';
                break;
            case 'فرز نجاری ( اور فرز )':
                $category = 'اور فرز نجاری';
                break;
            case 'اره افقی بر ( اره همه کاره )':
                $category = 'اره افقی';
                break;
            case 'اره عمود بر ( اره چکشی )':
                $category = 'اره عمود بر';
                break;
            case 'اره عمودبر (اره چکشی) گیربکسی':
                $category = 'اره عمود بر';
                break;
            case 'سنباده لرزان گرد':
                $category = 'سنباده لرزان';
                break;
            case 'سنباده لرزان مشتی':
                $category = 'سنباده لرزان';
                break;
            case 'سنباده لرزان تخت':
                $category = 'سنباده لرزان';
                break;
            case 'اره برقی':
                $category = 'اره زنجیری';
                break;
            case 'شمشاد زن برقی':
                $category = 'شمشاد زن';
                break;
            case 'بکس برقی و بکس شارژی':
                $category = 'آچار بکس برقی و شارژی';
                break;
            case 'اره گرد بر ( اره دیسکی )':
                $category = 'اره گرد بر';
                break;
            case 'بلوور ( دمنده و مکنده )':
                $category = 'بلوور (دمنده-مکنده)';
                break;
            case 'بلوور شارژی':
                $category = 'بلوور (دمنده-مکنده)';
                break;
            case 'رنده برقی نجاری':
                $category = 'رنده نجاری';
                break;
            case 'قیچی ورق بر برقی':
                $category = 'قیچی برقی';
                break;
            case 'سنباده لرزان گرد':
                $category = 'سنباده لرزان';
                break;
            case 'کارواش خانگی و صنعتی':
                $category = 'کارواش';
                break;
            
            default:
                
                break;
        }
        $category = str_replace(' ', '+', $category);
        $woo_cats = $woocommerce->get('products/categories?search=' . $category);
        $catID = $woo_cats[0]->id;
        $display_category = str_replace('+', ' ', $category);
        $title = $crawler->filter($settings['title'])->text();
        $attributes = $crawler->filter($settings['attributeContainer'])->each(function (Crawler $node) use ($settings) {
            return [
                'label' => trim($node->filter($settings['attributeLabel'])->text()),
                'value' => trim($node->filter($settings['attributeValue'])->text()),
            ];
        });
        $attributes[] = [
            'label' => 'برند',
            'value' => $brand,
        ];
        $weight = ''; // Initialize outside the loop

        foreach ($attributes as $attribute) {
            if ($attribute['label'] === 'وزن' || $attribute['label'] === 'وزن دقیق') {
                // Check if the value includes 'کیلوگرم' or 'گرم' and convert accordingly
                if (strpos($attribute['value'], 'کیلوگرم') === false) {
                    preg_match_all('/\d*\.?\d+/', $attribute['value'], $matches);
                    $grams = !empty($matches[0]) ? implode('', $matches[0]) : 0;
                    $weight = $grams / 1000; // Convert grams to kilograms
                    
                } else {
                    preg_match_all('/\d*\.?\d+/', $attribute['value'], $matches);
                    $weight = !empty($matches[0]) ? implode('', $matches[0]) : '';
                }
                break; // Exit the loop once the weight is found
            }
        }
      
        $imgUrls = $crawler->filter($settings['gallery'])->each(function (Crawler $node) {
            return $node->attr('src');
        });
     
        $content = $this->ContentModel->getContent($productUrl);
        $link_category = str_replace('+', '-', $category);
        $content['product_content'] .= '<p><span style="color: #000000;">سایر مدل های </span><a style="color: #37A75A;" href="https://abzarmakita.com/product-category/'.$link_category.'/"><strong>'.$display_category.'</strong></a> را میتوانید با کلیک بر روی آن مشاهده نمایید.</span></p><p><span style="color: #000000;"><span style="color: #37A75A;"><a style="color: #37A75A;" href="https://abzarmakita.com/"><strong>فروشگاه ابزارآلات اینترنتی ابزار ماکیتا</strong></a></span> ارائه دهنده انواع<span style="color: #37A75A;"> <a style="color: #37A75A;" href="https://abzarmakita.com/product-category/%d8%a7%d8%a8%d8%b2%d8%a7%d8%b1-%d8%a8%d8%b1%d9%82%db%8c-%d9%88-%d8%b4%d8%a7%d8%b1%da%98%db%8c/"><strong>ابزار برقی و شارژی</strong></a></span> از برند های مختلف از جمله <strong><span style="color: #37A75A;"><a style="color: #37A75A;" href="https://abzarmakita.com/brand/bosch/">بوش</a></span></strong><span style="color: #37A75A;"><strong>&nbsp;</strong></span>است.</span></p>';

        }catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        }

        $baseImagePath = $settings['baseIMG'];
        $storeName = parse_url($settings['store_url'], PHP_URL_HOST);
        $storeName = explode('.', $storeName)[0];
        
        $image_urls = []; // Initialize the array before the loop

        foreach ($imgUrls as $key => $imageUrl) {
            $result = "https://data.mr-h.net/" . $this->downloadAndModifyImage($imageUrl, $sku, $key + 1, $baseImagePath, $storeName);
            $image_urls[] = ['src' => $result]; // Append each new image URL to the array
        }
        
        try {
            $existing_attributes = $woocommerce->get('products/attributes');
            $attribute_ids = [];
            foreach ($existing_attributes as $attribute) {
                $attribute_ids[$attribute->name] = $attribute->id;
            }
 
        } catch (Exception $e) {
            echo "Error fetching attributes: " . $e->getMessage();
            exit; // Exit or handle the error gracefully
        }

        // Set up the product data
        return $product_data = [
            'name' => $title,
            'type' => 'simple',
            'regular_price' => strval($price),
            'description' => htmlspecialchars_decode($content['product_content']),
            'weight' => $weight,
            'sku' => $sku,
            'categories' => [
                [
                    'id' => $catID
                ],
            ],
            'images' => $image_urls,
            'attributes' => array_map(function ($attribute) use ($attribute_ids) {
                $attribute_name = $attribute['label'];
                $attribute_id = $attribute_ids[$attribute_name] ?? null;
                
                if ($attribute_id) {
                    // Use the global attribute ID
                    return [
                        'id' => $attribute_id,
                        'options' => [$attribute['value']],
                        'visible' => true,
                        'variation' => false
                    ];
                } else {
                    // Optionally handle attributes not found in WooCommerce
                    return [];
                }
            }, $attributes)
        ];

    }

    public function publishProductToWooCommerce($productData, $woocommerce) {
        try {
            $response = $woocommerce->post('products', $productData);
            
            // Log or inspect the response structure here for debugging
            // error_log(print_r($response, true));

            if (is_object($response) && isset($response->error)) {
                $errorMessage = $response->error;
                return ['error' => "Error creating product: " . $errorMessage];
            } elseif (is_array($response) && isset($response['error'])) {
                $errorMessage = $response['error'];
                return ['error' => "Error creating product: " . $errorMessage];
            } else {
                // Assuming the successful response includes an 'id' property
                return ['success' => 'Product created successfully. Product ID: <a href="https://abzarmakita.com/wp-admin/post.php?post=' . $response->id . '&action=edit" target="_blank">Edit Product</a>'];
            }
        } catch (\Exception $e) {
            return ['error' => "Error creating product: " . $e->getMessage()];
        } catch (\Throwable $t) {
            return ['error' => "Error creating product: " . $t->getMessage()];
        }
    }


    function downloadImage($imageUrl, $folderPath, $folderName, $index) {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL: $imageUrl");
        }
        $imageData = file_get_contents($imageUrl);
        if ($imageData === false) {
            throw new \Exception("Failed to download image: $imageUrl");
        }

        $filename = "$folderPath/$folderName-$index.jpg";
        if (!file_put_contents($filename, $imageData)) {
            throw new \Exception("Error saving image: $imageUrl");
        }

        return $filename;
    }

    function cropAndModifyImage($filename, $baseImagePath, $cropHeightPx, $bottomCropPx) {
        $source = imagecreatefromjpeg($filename);
        if ($source === false) {
            throw new \Exception("Failed to create source image from $filename");
        }

        $width = imagesx($source);
        $originalHeight = imagesy($source);
        $height = $originalHeight - $cropHeightPx - $bottomCropPx;

        if ($height > 0) {
            $cropped = imagecrop($source, ['x' => 0, 'y' => $cropHeightPx, 'width' => $width, 'height' => $height]);
            if ($cropped !== false) {
                imagejpeg($cropped, $filename);
                imagedestroy($cropped);
            }
        }
        imagedestroy($source);

        $this->overlayImage($filename, $baseImagePath);
    }

    function overlayImage($filename, $baseImagePath) {
        $baseImage = imagecreatefromjpeg($baseImagePath);
        $overlayImage = imagecreatefromjpeg($filename);
        if ($overlayImage === false) {
            throw new \Exception("Failed to create overlay image from $filename");
        }

        $baseWidth = imagesx($baseImage);
        $baseHeight = imagesy($baseImage);
        $overlayWidth = imagesx($overlayImage);
        $overlayHeight = imagesy($overlayImage);

        $destX = round(($baseWidth - $overlayWidth) / 2);
        $destY = round(($baseHeight - $overlayHeight) / 4);

        imagecopy($baseImage, $overlayImage, $destX, $destY, 0, 0, $overlayWidth, $overlayHeight);
        imagejpeg($baseImage, $filename);

        imagedestroy($baseImage);
        imagedestroy($overlayImage);
    }

    function downloadAndModifyImage($imageUrl, $folderName, $index, $baseImagePath, $storeName) {
        $folderPath = "img/products/$storeName/$folderName";
        if (!is_dir($folderPath) && !mkdir($folderPath, 0777, true)) {
            throw new \Exception("Error creating folder: $folderPath");
        }

        try {
            $dpi = 72; // Assuming 72 DPI
            $cropHeightPx = 2.29 * ($dpi / 2.54); // Height to crop from the top
            $bottomCropPx = 45; // Height to crop from the bottom

            $filename = $this->downloadImage($imageUrl, $folderPath, $folderName, $index);
            $this->cropAndModifyImage($filename, $baseImagePath, $cropHeightPx, $bottomCropPx);

            return $filename;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    

}
