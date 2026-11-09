<?php

namespace Tests;

use Souravmsh\Html\Services\FormBuilder;

class FormBuilderTest extends TestCase
{
    protected FormBuilder $form;

    public function setUp(): void
    {
        $this->form = new FormBuilder();
    }

    public function testFileTwoArgsAndThreeArgsNullProduceIdenticalOutput(): void
    {
        // 1. Standard 2-argument call: Form::file('file', ['class' => 'form-control'])
        $standard = (string) \Souravmsh\Html\Facades\Form::file('file', ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="file" id="file" class="form-control">', $standard);

        // 2. 3-argument call with null: Form::file('file', null, ['class' => 'form-control'])
        $threeArgs = (string) \Souravmsh\Html\Facades\Form::file('file', null, ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="file" id="file" class="form-control">', $threeArgs);

        // Both must be completely identical
        $this->assertEquals($standard, $threeArgs);
    }

    public function testFileBasic(): void
    {
        $html = (string) $this->form->file('avatar');
        $this->assertEquals('<input type="file" name="avatar" id="avatar">', $html);
    }

    public function testFileWithAttributes(): void
    {
        $html = (string) $this->form->file('avatar', ['class' => 'form-control', 'accept' => 'image/*']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">', $html);
    }

    public function testFileWithNullAttributes(): void
    {
        $html = (string) $this->form->file('avatar', null);
        $this->assertEquals('<input type="file" name="avatar" id="avatar">', $html);
    }

    public function testFileWithThreeArgumentsNullValue(): void
    {
        $html = (string) $this->form->file('avatar', null, ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control">', $html);
    }

    public function testFileWithThreeArgumentsStringValueIgnored(): void
    {
        $html = (string) $this->form->file('avatar', 'some_file_value.png', ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control">', $html);
    }

    public function testFileWithTwoArgumentsStringValueIgnored(): void
    {
        $html = (string) $this->form->file('avatar', 'some_file_value.png');
        $this->assertEquals('<input type="file" name="avatar" id="avatar">', $html);
    }

    public function testFileWithExplicitValueInAttributesIgnored(): void
    {
        $html = (string) $this->form->file('avatar', null, ['value' => 'ignored.png', 'class' => 'form-control']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control">', $html);
    }

    public function testFileWithThreeArgumentsEmptyStringValue(): void
    {
        $html = (string) $this->form->file('avatar', '', ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control">', $html);
    }

    public function testFileNeverOutputsValueAttributeEvenWithModelBinding(): void
    {
        $user = ['avatar' => 'path/to/old-avatar.jpg'];
        $this->form->model($user);

        $html = (string) $this->form->file('avatar', ['class' => 'form-control']);
        $this->assertEquals('<input type="file" name="avatar" id="avatar" class="form-control">', $html);

        $this->form->close();
    }

    public function testPasswordWithNullAttributes(): void
    {
        $html = (string) $this->form->password('password', null);
        $this->assertEquals('<input type="password" name="password" id="password">', $html);
    }

    public function testPasswordWithThreeArguments(): void
    {
        $html = (string) $this->form->password('password', null, ['class' => 'form-control']);
        $this->assertEquals('<input type="password" name="password" id="password" class="form-control">', $html);
    }

    public function testPasswordWithEmptyStringValue(): void
    {
        $html = (string) $this->form->password('password', '', ['class' => 'form-control']);
        $this->assertEquals('<input type="password" name="password" id="password" class="form-control">', $html);
    }

    public function testTextWithNullAttributes(): void
    {
        $html = (string) $this->form->text('username', 'john_doe', null);
        $this->assertEquals('<input type="text" name="username" id="username" value="john_doe">', $html);
    }

    public function testTextareaWithNullAttributes(): void
    {
        $html = (string) $this->form->textarea('bio', 'Hello', null);
        $this->assertEquals('<textarea name="bio" id="bio" rows="5" cols="50">Hello</textarea>', $html);
    }

    public function testSubmitAndButtonWithNullAttributes(): void
    {
        $submit = (string) $this->form->submit('Save', null);
        $this->assertEquals('<input type="submit" name="" id="" value="Save">', $submit);

        $button = (string) $this->form->button('Click Me', null);
        $this->assertEquals('<button type="button">Click Me</button>', $button);
    }
}
