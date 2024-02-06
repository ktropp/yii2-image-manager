<?php

namespace noam148\imagemanager\components;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use noam148\imagemanager\models\ImageManager;
use noam148\imagemanager\assets\ImageManagerInputAsset;

class ImageManagerInputWidget extends InputWidget {

    /**
     * @var null|integer The aspect ratio the image needs to be cropped in (optional)
     */
    public $aspectRatio = null; //option info: https://github.com/fengyuanchen/cropper/#aspectratio

    /**
     * @var int Define the viewMode of the cropper
     */
    public $cropViewMode = 1; //option info: https://github.com/fengyuanchen/cropper/#viewmode

    /**
     * @var bool Show a preview of the image under the input
     */
    public $showPreview = true;

    /**
     * @var bool Show a confirmation message when de-linking a image from the input
     */
    public $showDeletePickedImageConfirm = false;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        //set language
        if (!isset(Yii::$app->i18n->translations['imagemanager'])) {
            Yii::$app->i18n->translations['imagemanager'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@noam148/imagemanager/messages'
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function run() {
        //default
        $ImageManager_id = null;
        $mImageManager = null;
        $sFieldId = null;
        //start input group
        $field = "<div class='image-manager-input'>";
        $field .= "<div class='input-group'>";
        //set input fields
        if ($this->hasModel()) {
            //get field id
            $sFieldId = Html::getInputId($this->model, $this->attribute);
            $sFieldNameId = $sFieldId . "_name";
            //get attribute name
            $sFieldAttributeName = Html::getAttributeName($this->attribute);
            //get filename from selected file
            $ImageManager_id = $this->model->{$sFieldAttributeName};
            $ImageManager_fileName = null;
            $mImageManager = ImageManager::findOne($ImageManager_id);
            if ($mImageManager !== null) {
                $ImageManager_fileName = $mImageManager->fileName;
            }
            //create field
            $field .= Html::textInput($this->attribute, $ImageManager_fileName, ['class' => 'form-control', 'id' => $sFieldNameId, 'readonly' => true]);
            $field .= Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $sFieldId = $this->name;
            $sFieldId = strtolower($sFieldId);
            $sFieldId = str_replace("][", '-', $sFieldId);
            $sFieldId = str_replace("[", '-', $sFieldId);
            $sFieldId = str_replace("]", '', $sFieldId);
            $sFieldNameId = $sFieldId . "_name";

            $ImageManager_fileName = null;
            $mImageManager = ImageManager::findOne($this->value);
            if ($mImageManager !== null) {
                $ImageManager_fileName = $mImageManager->fileName;
            }

            $field .= Html::textInput($this->name . "_name", $ImageManager_fileName, ['readonly' => true, 'id' => $sFieldNameId, 'class' => 'form-control',]);
            $field .= Html::hiddenInput($this->name, $this->value, $this->options);

        }
        //end input group
        $sHideClass = $ImageManager_id === null ? 'hide' : '';
        $field .= "<a href='#' class='input-group-addon btn btn-primary delete-selected-image " . $sHideClass . "' data-input-id='" . $sFieldId . "' data-show-delete-confirm='" . ($this->showDeletePickedImageConfirm ? "true" : "false") . "'>" . '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg>' . "</a>";
        $field .= "<a href='#' class='input-group-addon btn btn-primary open-modal-imagemanager' data-aspect-ratio='" . $this->aspectRatio . "' data-crop-view-mode='" . $this->cropViewMode . "' data-input-id='" . $sFieldId . "'>";
        $field .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder2-open" viewBox="0 0 16 16">
  <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z"/>
</svg>';
        $field .= "</a></div>";

        //show preview if is true
        if ($this->showPreview == true) {
            $sHideClass = ($mImageManager == null) ? "hide" : "";
            $sImageSource = isset($mImageManager->id) ? \Yii::$app->imagemanager->getImagePath($mImageManager->id, 500, 500, 'inset') : "";
            $sImageFull = isset($mImageManager->id) ? \Yii::$app->imagemanager->getImagePath($mImageManager->id, 1024, 1024, 'inset') : "";

            $field .= '<div class="image-wrapper ' . $sHideClass . '">'
                . '<a target="_blank" href="' . $sImageFull . '">'
                . '<img id="' . $sFieldId . '_image" alt="Thumbnail" class="img-responsive img-preview" src="' . $sImageSource . '">'
                . '</a>'
                . '</div>';
        }

        //close image-manager-input div
        $field .= "</div>";

        echo $field;

        $this->registerClientScript();
    }

    /**
     * Registers js Input
     */
    public function registerClientScript() {
        $view = $this->getView();
        ImageManagerInputAsset::register($view);

        //set baseUrl from image manager
        $sBaseUrl = Url::to(['/imagemanager/manager']);
        //set base url
        $view->registerJs("imageManagerInput.baseUrl = '" . $sBaseUrl . "';");
        $view->registerJs("imageManagerInput.message = " . Json::encode([
                    'imageManager' => Yii::t('imagemanager','Image manager'),
                    'detachWarningMessage' => Yii::t('imagemanager', 'Are you sure you want to detach the image?'),
                ]) . ";");
    }

}
