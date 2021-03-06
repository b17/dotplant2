<?php

namespace app\backend\widgets;

use app\models\Object;
use app\modules\review\models\Review;
use Yii;
use yii\base\Widget;
use app;
use kartik\icons\Icon;
use yii\helpers\Json;

class FloatingPanel extends Widget
{
    public $bottom = false;

    /**
     * Check if model has any reviews and if so - generate link to edit them
     *
     * @param string $objectName
     * @param int $modelId
     * @return array
     */
    private function getReviewEditParams($objectName, $modelId)
    {
        $objectId = array_search($objectName, Object::getSelectArray());
        $reviews = Review::getForObjectModel($modelId, $objectId, 1);
        if (!empty($reviews)) {
            return [
                "label" => Icon::show("pencil") . Yii::t("app", "Edit reviews") . " (" . count($reviews) . ")",
                "url" => [
                    "/review/backend-review/index",
                    "SearchModel" => [
                        "object_id" => $objectId,
                        "object_model_id" => $modelId
                    ]
                ],
                "target" => "_blank"
            ];
        } else {
            return [];
        }
    }

    public function run()
    {
        app\backend\assets\FrontendEditingAsset::register($this->view);

        $items = [
            [
                'label' => Icon::show('dashboard') . ' ' . Yii::t('app', 'Backend'),
                'url' => ['/backend/'],
            ]
        ];

        switch (Yii::$app->requestedRoute) {
            case 'shop/product/list':
                if (isset($_GET['properties'])) {
                    $apply_if_params = [];
                    foreach ($_GET['properties'] as $property_id => $values) {
                        if (isset($values[0])) {
                            $apply_if_params[$property_id] = $values[0];
                        }
                    }
                    if (Yii::$app->response->dynamic_content_trait === true) {
                        $items[] = [
                            'label' => Icon::show('puzzle') . ' ' . Yii::t('app', 'Edit Dynamic Content'),
                            'url' => [
                                '/backend/dynamic-content/edit',
                                'id' => Yii::$app->response->matched_dynamic_content_trait_model->id,
                            ],
                        ];
                    } else {
                        if (isset($_GET['properties'], $_GET['last_category_id'])) {
                            $items[] = [
                                'label' => Icon::show('puzzle') . ' ' . Yii::t('app', 'Add Dynamic Content'),
                                'url' => [
                                    '/backend/dynamic-content/edit',
                                    'DynamicContent' => [
                                        'apply_if_params' => Json::encode($apply_if_params),
                                        'apply_if_last_category_id' => $_GET['last_category_id'],
                                        'object_id' => Object::getForClass(app\modules\shop\models\Product::className())->id,
                                        'route' => 'shop/product/list',
                                    ]
                                ],
                            ];

                        }

                    }
                } else {
                    // no properties selected - go to category edit page

                    if (isset($_GET['last_category_id'])) {
                        $reviewsLink = $this->getReviewEditParams("Category", intval($_GET['last_category_id']));
                        $cat = app\modules\shop\models\Category::findById($_GET['last_category_id']);
                        $items[] = [
                            'label' => Icon::show('pencil') . ' ' . Yii::t('app', 'Edit category'),
                            'url' => [
                                '/shop/backend-category/edit',
                                'id' => $cat->id,
                                'parent_id' => $cat->parent_id,
                            ],
                        ];
                    }
                }

                break;
            case 'shop/product/show':
                if (isset($_GET['model_id'])) {
                    $reviewsLink = $this->getReviewEditParams("Product", intval($_GET['model_id']));
                    $items[] = [
                        'label' => Icon::show('pencil') . ' ' . Yii::t('app', 'Edit product'),
                        'url' => [
                            '/shop/backend-product/edit',
                            'id' => intval($_GET['model_id'])
                        ],
                    ];
                }
                break;
            
            case '/page/page/show':
            case '/page/page/list':
                if (isset($_GET['id'])) {
                    $page = app\modules\page\models\Page::findById($_GET['id']);
                    $reviewsLink = $this->getReviewEditParams("Page", $_GET['id']);
                    $items[] = [
                        'label' => Icon::show('pencil') . ' ' . Yii::t('app', 'Edit page'),
                        'url' => [
                            '/page/backend/edit',
                            'id' => $page->id,
                            'parent_id' =>$page->parent_id,

                        ],
                    ];
                }
                break;
        }

        if (!empty($reviewsLink)) {
            $items[] = $reviewsLink;
        }

        return $this->render(
            'floating-panel',
            [
                'items' => $items,
                'bottom' => $this->bottom,
            ]
        );

    }
}