<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 11:49
 */

namespace CodeLovers\ExamSignupBundle\Export;


use CodeLovers\ExamSignupBundle\Entity\Exam;
use CodeLovers\ExamSignupBundle\Entity\ExamDate;
use PHPExcel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\TranslatorInterface;

class ExcelExport
{
    /**
     * @var string
     */
    private $cacheFolder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @param string $cacheFolder
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct($cacheFolder, TranslatorInterface $translator)
    {
        $this->cacheFolder = $cacheFolder;
        $this->fs = new Filesystem();
        if (!$this->fs->exists($cacheFolder)) {
            $this->fs->mkdir($cacheFolder);
        }
        $this->translator = $translator;
    }

    /**
     * @param Exam $exam
     *
     * @return string
     */
    public function export(Exam $exam)
    {
        $excel = new PHPExcel();
        $properties = $excel->getProperties();
        $properties->setCreated(null);
        $properties->setCreator('CodeLovers Exam Signup')
            ->setTitle((string) $exam);

        $excel->setActiveSheetIndex(0);

        $sheet = $excel->getActiveSheet();
        $sheet->SetCellValue('A1', (string) $exam);

        $sheet->SetCellValue('A3', $this->translator->trans('examDate.location'));
        $sheet->SetCellValue('B3', $this->translator->trans('examDate.date'));
        $sheet->SetCellValue('C3', $this->translator->trans('examDate.participant'));
        $sheet->SetCellValue('D3', $this->translator->trans('users.username'));


        $row = 3;
        $lastDay = null;
        foreach ($exam->getDates() as $date) {
            /** @var ExamDate $date */
            $currentDay = $date->getDate()->format('Y-m-d');

            if ($lastDay != $currentDay) {
                $lastDay = $currentDay;
                $row++;
            }

            $sheet->SetCellValue('A' . $row, $date->getLocation());
            $sheet->SetCellValue('B' . $row, $date->getDate()->format('d.m.Y H:i'));

            if ($registration = $date->getRegistration()) {
                $sheet->SetCellValue('C' . $row, (string)$registration);
                $sheet->SetCellValue('D' . $row, $registration->getUser()->getUsername());
            }
            $row++;
        }

        $tmpFile = sprintf("%s/%d", $this->cacheFolder, $exam->getId());
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->save($tmpFile);

        $output = file_get_contents($tmpFile);
        $this->fs->remove($tmpFile);

        return $output;
    }
} 