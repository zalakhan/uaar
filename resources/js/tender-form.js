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

const TITLE_CATEGORIES = ['Purchase', 'Auction'];
const DETAIL_CATEGORIES = ['Tender', 'Quotation', 'Pre-Qualification'];

document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('category');
    const form = document.getElementById('tender-form');

    if (!categorySelect || !form) {
        return;
    }

    const fields = {
        title: document.getElementById('field-title'),
        tenderNo: document.getElementById('field-tender-no'),
        description: document.getElementById('field-description'),
        uploadedDate: document.getElementById('field-uploaded-date'),
        dueDate: document.getElementById('field-due-date'),
        tenderFile: document.getElementById('field-tender-file'),
    };

    const inputs = {
        title: document.getElementById('title'),
        tenderNo: document.getElementById('tender_no'),
        description: document.getElementById('description'),
        uploadedDate: document.getElementById('uploaded_date'),
        dueDate: document.getElementById('due_date'),
        tenderFile: document.getElementById('tender_file'),
    };

    let descriptionEditor = null;

    function setFieldState(wrapper, input, visible, required) {
        if (!wrapper) {
            return;
        }

        wrapper.classList.toggle('d-none', !visible);

        if (!input) {
            return;
        }

        input.disabled = !visible;
        input.required = visible && required;
    }

    async function initDescriptionEditor() {
        if (descriptionEditor || !inputs.description || inputs.description.disabled) {
            return;
        }

        descriptionEditor = await ClassicEditor.create(inputs.description, {
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
        });

        descriptionEditor.model.document.on('change:data', () => {
            inputs.description.value = descriptionEditor.getData();
        });
    }

    async function destroyDescriptionEditor() {
        if (!descriptionEditor) {
            return;
        }

        await descriptionEditor.destroy();
        descriptionEditor = null;
    }

    async function updateFieldVisibility() {
        const category = categorySelect.value;

        const showTitle = TITLE_CATEGORIES.includes(category);
        const showDetails = DETAIL_CATEGORIES.includes(category);
        const showDatesAndFile = category !== '0';

        setFieldState(fields.title, inputs.title, showTitle, true);
        setFieldState(fields.tenderNo, inputs.tenderNo, showDetails, true);
        setFieldState(fields.description, inputs.description, showDetails, true);
        setFieldState(fields.uploadedDate, inputs.uploadedDate, showDatesAndFile, true);
        setFieldState(fields.dueDate, inputs.dueDate, showDatesAndFile, true);
        setFieldState(fields.tenderFile, inputs.tenderFile, showDatesAndFile, inputs.tenderFile?.dataset.requiredOnCreate === '1');

        if (showDetails) {
            await initDescriptionEditor();
        } else {
            await destroyDescriptionEditor();
            if (inputs.description) {
                inputs.description.value = '';
            }
        }
    }

    categorySelect.addEventListener('change', () => {
        updateFieldVisibility();
    });

    form.addEventListener('submit', () => {
        if (descriptionEditor) {
            inputs.description.value = descriptionEditor.getData();
        }
    });

    updateFieldVisibility();
});
