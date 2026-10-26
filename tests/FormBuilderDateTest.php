<?php

namespace Tests;

use DateTime;
use DateTimeImmutable;
use Souravmsh\Html\Services\FormBuilder;

class FormBuilderDateTest extends TestCase
{
    protected FormBuilder $form;

    public function setUp(): void
    {
        $this->form = new FormBuilder();
    }

    public function testDateBasic(): void
    {
        $html = (string) $this->form->date('birth_date');
        $this->assertEquals('<input type="date" name="birth_date" id="birth_date">', $html);
    }

    public function testDateWithStringValue(): void
    {
        $html = (string) $this->form->date('birth_date', '2026-10-26', ['class' => 'form-control']);
        $this->assertEquals('<input type="date" name="birth_date" id="birth_date" value="2026-10-26" class="form-control">', $html);
    }

    public function testDateWithDateTimeInstance(): void
    {
        $date = new DateTime('1995-10-26 14:30:00');
        $html = (string) $this->form->date('birth_date', $date);
        $this->assertEquals('<input type="date" name="birth_date" id="birth_date" value="1995-10-26">', $html);
    }

    public function testDateWithDateTimeImmutableInstance(): void
    {
        $date = new DateTimeImmutable('2000-01-15 08:00:00');
        $html = (string) $this->form->date('dob', $date);
        $this->assertEquals('<input type="date" name="dob" id="dob" value="2000-01-15">', $html);
    }

    public function testDatetimeWithDateTimeInstance(): void
    {
        $dt = new DateTime('2026-10-26T15:30:00+00:00');
        $html = (string) $this->form->datetime('scheduled_at', $dt);
        $expected = $dt->format(\DateTimeInterface::RFC3339);
        $this->assertEquals('<input type="datetime" name="scheduled_at" id="scheduled_at" value="' . $expected . '">', $html);
    }

    public function testDatetimeLocalWithDateTimeInstance(): void
    {
        $dt = new DateTime('2026-10-26 15:30:00');
        $html = (string) $this->form->datetimeLocal('meeting_time', $dt, ['class' => 'form-control']);
        $this->assertEquals('<input type="datetime-local" name="meeting_time" id="meeting_time" value="2026-10-26T15:30" class="form-control">', $html);
    }

    public function testTimeWithDateTimeInstance(): void
    {
        $dt = new DateTime('2026-10-26 09:45:00');
        $html = (string) $this->form->time('start_time', $dt);
        $this->assertEquals('<input type="time" name="start_time" id="start_time" value="09:45">', $html);
    }

    public function testModelBindingWithDates(): void
    {
        $user = (object) [
            'dob'        => new DateTime('1990-05-20 00:00:00'),
            'meeting'    => new DateTime('2026-12-01 10:15:00'),
            'alarm_time' => new DateTime('2026-01-01 07:30:00'),
        ];

        $this->form->model($user);

        $dateHtml = (string) $this->form->date('dob');
        $this->assertEquals('<input type="date" name="dob" id="dob" value="1990-05-20">', $dateHtml);

        $dtHtml = (string) $this->form->datetimeLocal('meeting');
        $this->assertEquals('<input type="datetime-local" name="meeting" id="meeting" value="2026-12-01T10:15">', $dtHtml);

        $timeHtml = (string) $this->form->time('alarm_time');
        $this->assertEquals('<input type="time" name="alarm_time" id="alarm_time" value="07:30">', $timeHtml);

        $this->form->close();
    }

    public function testFormHelperFunction(): void
    {
        $this->assertTrue(function_exists('form'));
        $instance = form();
        $this->assertTrue($instance instanceof FormBuilder);
        $date = (string) form()->date('dob', '2026-10-26');
        $this->assertEquals('<input type="date" name="dob" id="dob" value="2026-10-26">', $date);
    }
}
