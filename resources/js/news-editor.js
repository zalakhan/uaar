import {
    Alignment,
    Bold,
    ClassicEditor,
    Essentials,
    Heading,
    Italic,
    Link,
    List,
    Paragraph,
    Underline,
    Undo,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('description');

    if (! textarea) {
        return;
    }

    const form = textarea.closest('form');

    ClassicEditor.create(textarea, {
        licenseKey: 'GPL',
        plugins: [
            Essentials,
            Bold,
            Italic,
            Underline,
            Paragraph,
            Heading,
            List,
            Link,
            Alignment,
            Undo,
        ],
        toolbar: [
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'bold', 'italic', 'underline',
            '|',
            'bulletedList', 'numberedList',
            '|',
            'alignment',
            '|',
            'link',
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            ],
        },
        link: {
            addTargetToExternalLinks: true,
        },
    }).then((editor) => {
        editor.model.document.on('change:data', () => {
            textarea.value = editor.getData();
        });

        if (form) {
            form.addEventListener('submit', () => {
                textarea.value = editor.getData();
            });
        }
    }).catch((error) => {
        console.error('CKEditor failed to initialize:', error);
    });
});