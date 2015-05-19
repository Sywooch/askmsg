<?php
/**
 * User: KozminVA
 * Date: 07.04.2015
 * Time: 15:23
 *
 *
 * @var Message $model
 */

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\Message;
use app\components\Exportutil;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

use yii\helpers\Html;

    $oUtil = new Exportutil();
    $sFilename = 'message-'.$model->msg_id.'.docx';
    $sf = $oUtil->getFilePath($sFilename);

    $phpWord = new PhpWord();

    $n1sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1/2.54);
    $n1_5sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1.5/2.54);
    $n0_4sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.4/2.54);
    $n2sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(2/2.54);
    $n5_2sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(5.2/2.54);
    $n15sm = \PhpOffice\PhpWord\Shared\Converter::inchToTwip(15/2.54);

    $aStyleHead = [
        'font' => [
            'size' => 12,
        ],
        'paragraph' => [
            'align'      => 'right',
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
            'space' => [
                'before' => 100,
                'after' => 50
            ],
            'indentation' => [
                'left' => 5040,
                'right' => 20,
                'firstLine' => 0
            ]
        ],
    ];

    $aStyleTitle = [
        'font' => [
            'size' => 20,
        ],
        'paragraph' => [
            'align'      => 'center',
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(48),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(24),
        ],
    ];

    $aStyleTitle1 = [
        'font' => [
            'size' => 20,
        ],
        'paragraph' => [
            'align'      => 'left',
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(48),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
        ],
    ];

    $aStyleTitle2 = [
        'font' => [
            'size' => 20,
        ],
        'paragraph' => [
            'align'      => 'left',
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
        ],
    ];

    $aStyleText = [
        'font' => [
            'size' => 12,
        ],
        'paragraph' => [
            'align'      => 'both',
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
        ],
    ];

    $sectionStyle = array(
        'orientation' => 'portrait',
        'marginTop' => $n1sm,
        'marginBottom' => $n1sm,
        'marginLeft' => $n1sm,
        'marginRight' => $n1sm,
    );

    $section = $phpWord->addSection($sectionStyle);
    $phpWord->setDefaultParagraphStyle(
        array(
            'align'      => 'both',
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
            'spacing'    => 120,
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
            'indentation' => ['firstLine' => 240],
        )
    );

    $section->addText(
        htmlspecialchars($model->getFullName()),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars($model->msg_pers_email),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars($model->msg_pers_phone),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars($model->region->reg_name),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars($model->msg_pers_org),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars(date('d.m.Y', strtotime($model->msg_createtime))),
        $aStyleHead['font'],
        $aStyleHead['paragraph']
    );

    $section->addText(
        htmlspecialchars('Обращение № ' . $model->msg_id),
        $aStyleTitle['font'],
        $aStyleTitle['paragraph']
    );

    $a = explode("\n", strip_tags(Html::decode($model->msg_pers_text)));
    foreach($a As $v) {
        $v = trim($v);
        if( $v == '' ) {
            continue;
        }
        $section->addText(
            htmlspecialchars($v),
            $aStyleText['font'],
            $aStyleText['paragraph']
        );
    }

    if( !empty($model->msg_empl_id) ) {
        $section->addText(
            htmlspecialchars('Исполнитель'),
            $aStyleTitle1['font'],
            $aStyleTitle1['paragraph']
        );
        $section->addText(
            htmlspecialchars($model->employee->getFullName()),
            $aStyleText['font'],
            $aStyleText['paragraph']
        );
    }

    if( !empty($model->msg_empl_command) ) {
        $section->addText(
            htmlspecialchars('Поручение'),
            $aStyleTitle2['font'],
            $aStyleTitle2['paragraph']
        );
        $section->addText(
            htmlspecialchars($model->msg_empl_command),
            $aStyleText['font'],
            $aStyleText['paragraph']
        );
    }

    /*
    $section->addText(
        htmlspecialchars(
            'Paragraph with keepNext = true (default: false). '
            . '"Keep with next" is used to prevent Word from inserting automatic page '
            . 'breaks between paragraphs. Set this option to "true" if you do not want '
            . 'your paragraph to be separated with the next paragraph.'
        ),
        null,
        array('keepNext' => true, 'indentation' => array('firstLine' => 240))
    );

    $section->addText(
        htmlspecialchars(
            'Paragraph with keepLines = true (default: false). '
            . '"Keep lines together" will prevent Word from inserting an automatic page '
            . 'break within a paragraph. Set this option to "true" if you do not want '
            . 'all lines of your paragraph to be in the same page.'
        ),
        null,
        array('keepLines' => true, 'indentation' => array('left' => 240, 'hanging' => 240))
    );
*/


    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($sf);

    echo $sf;

//    Yii::$app->response->sendFile($sf);


