<?php
/**
 * Created by PhpStorm.
 * User: Yang DingChuan
 * Date: 2017/3/16
 * Time: 15:43
 */
namespace frontend\controllers;

use frontend\modules\admin\models\searchs\User;
use Yii;
use yii\helpers\Url;
use yii\base\Exception;
use yii\data\Pagination;
use common\models\UserModel;
use yii\filters\AccessControl;
use common\models\ArticleModel;
use common\components\CommonHelper;
use common\models\ArticleReviewModel;
use common\models\ArticleContentModel;

/**
 * Class ArticleController
 * @package frontend\controllers
 */
class ArticleController extends BaseController
{
    /**
     * 行为控制
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $category = Yii::$app->request->get('category');

        $query = $queryCount = ArticleModel::find()
            ->andFilterWhere(['category' => $category])
            ->asArray();

        $count = $queryCount->count();

        /* 分页 */
        $pages = new Pagination(['totalCount' =>$count]);

        $sort = Yii::$app->request->get('sort');
        switch ($sort) {
            case 1 :
                $order = 'visit_times DESC';
                break;
            case 2 :
                $order = 'review_times';
                break;
            default:
                $order = 'id DESC';
                break;
        }

        //统计
        $data = ['0' => ArticleModel::find()->count()];
        foreach (Yii::$app->params['articleCategory'] as $k => $v) {
            $data[$k] = ArticleModel::find()
                ->andFilterWhere(['category' => $k])
                ->count();
        }

        $result = $query->offset($pages->offset)->limit($pages->limit)->orderBy($order)->all();

        //用户信息
        $userIdList = array_column($result, 'user_id');
        $userList = CommonHelper::getUserList($userIdList);

        return $this->render('index', [
            'result' => $result,
            'userList' => $userList,
            'chart' => $data,
            'pages' => $pages
        ]);
    }

    public function actionCreate()
    {
        $model = new ArticleModel();
        $contentModel = new ArticleContentModel();

        if (Yii::$app->request->getIsPost()) {
            $userId = Yii::$app->user->id;

            //事物
            $tran = Yii::$app->db->beginTransaction();
            try {
                $model->load(Yii::$app->request->post());
                $model->user_id = $userId;

                if (!$model->save()) {
                    throw new Exception('添加文章失败');
                }

                $content = Yii::$app->request->post('content');
                $contentModel->content = $content;
                $contentModel->article_id = $model->id;

                if (!$contentModel->save()) {
                    throw new Exception('添加文章失败');
                }

                CommonHelper::setFlash('success', '添加成功');

                $tran->commit();
                $this->redirect('index.html')->send();

            } catch (Exception $e) {
                $tran->rollBack();
                CommonHelper::setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'contentModel' => $contentModel
        ]);
    }

    public function actionView()
    {
        $id = Yii::$app->request->get('id');

        $article = ArticleModel::findOne($id);
        $content = ArticleContentModel::findOne($id);
        $user = UserModel::findOne($article->user_id);

        //没有信息跳转404
        if (empty($article) || empty($content)) {
            $this->redirect(['site/error'])->send();
        }

        //回复model
        $reviewModel = new ArticleReviewModel();

        if (Yii::$app->request->post()) {
            $reviewModel->load(Yii::$app->request->post());

            $reviewModel->article_id = $id;
            $reviewModel->user_id = Yii::$app->user->id;

            if ($reviewModel->save()) {
                $article->review_times += 1;

                $article->save();
            }

            $this->redirect(Url::current(['#'=>'review-list']))->send();
        }

        //浏览次数+1
        $article->visit_times += 1;
        $article->save();

        $reviewList = ArticleReviewModel::find()
            ->select(['article_review.*', 'user.username', 'user.pic_small'])
            ->innerJoin(UserModel::tableName(), 'user.id = article_review.user_id')
            ->andWhere(['article_id' => $id])
            ->orderBy('article_review.id DESC')
            ->asArray()
            ->all();

        return $this->render('view', [
            'model' => $article,
            'content' => $content,
            'user' => $user,
            'reviewList' => $reviewList,
            'reviewModel' => $reviewModel,
        ]);
    }

    public function actionTest()
    {
        echo 123;die;
    }
}