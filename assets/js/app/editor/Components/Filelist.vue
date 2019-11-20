<template>
    <div>
        <div
                v-for="(child, index) in containerFiles"
                :key="index"
                class="form-fieldsgroup"
        >
            <editor-file
                    v-if="child.hidden !== true"
                    :filename="child.filename"
                    :thumbnail="child.thumbnail"
                    :title="child.title"
                    :alt="child.alt"
                    :attributes-link="attributesLink"
                    :media="child.media"
                    :directory="directory"
                    :filelist="filelist"
                    :csrf-token="csrfToken"
                    :labels="labels"
                    :in-filelist="true"
                    :name="fieldName(index)"
                    :extensions="extensions"
                    :filelist-position="index"
                    :is-first-in-filelist="isFirstInFilelist(index)"
                    :is-last-in-filelist="isLastInFilelist(index)"
                    @remove="onRemoveFile"
                    @updated="onUpdateFile"
                    @moveFileUp="onMoveFileUp"
                    @moveFileDown="onMoveFileDown"
            ></editor-file>
        </div>

        <button class="btn btn-secondary" type="button" @click="addFile">
            Add file here
        </button>
    </div>
</template>

<script>
    import File from './File';

    export default {
        name: 'EditorFile',
        components: { 'editor-file': File },
        props: [
            'files',
            'directory',
            'name',
            'filelist',
            'csrfToken',
            'labels',
            'extensions',
            'attributesLink',
        ],
        data: function() {
            return {
                containerFiles: this.files,
            };
        },
        methods: {
            isFirstInFilelist(index) {
                return index === 0;
            },
            isLastInFilelist(index) {
                return index === this.getActiveFileFields().length - 1;
            },
            getActiveFileFields() {
                return this.containerFiles.filter(function(file) {
                    return file.hidden !== true;
                });
            },
            getFieldNumberFromElement(elem) {
                return parseInt(elem.fieldName.match(/\d+/)[0]);
            },
            onMoveFileDown(elem) {
                let fieldNumber = this.getFieldNumberFromElement(elem);

                if (fieldNumber < this.containerFiles.length - 1) {
                    let fileToMoveDown = this.containerFiles[fieldNumber];
                    let fileToMoveUp = this.containerFiles[fieldNumber + 1];

                    this.containerFiles.splice(
                        fieldNumber,
                        2,
                        fileToMoveUp,
                        fileToMoveDown,
                    );
                }
            },
            onMoveFileUp(elem) {
                let fieldNumber = this.getFieldNumberFromElement(elem);

                if (fieldNumber > 0) {
                    let fileToMoveUp = this.containerFiles[fieldNumber];
                    let fileToMoveDown = this.containerFiles[fieldNumber - 1];

                    this.containerFiles.splice(
                        fieldNumber - 1,
                        2,
                        fileToMoveUp,
                        fileToMoveDown,
                    );
                }
            },
            onUpdateFile(elem) {
                let fieldNumber = this.getFieldNumberFromElement(elem);
                this.containerFiles[fieldNumber] = elem;
            },
            onRemoveFile(elem) {
                let fieldNumber = this.getFieldNumberFromElement(elem);
                let updatedFile = this.containerFiles[fieldNumber];
                updatedFile.hidden = true;
                this.$set(this.containerFiles, fieldNumber, updatedFile);

                if (this.getActiveFileFields().length === 0) {
                    this.addFile();
                }
            },
            fieldName(index) {
                return this.name + '[' + index + ']';
            },
            addFile() {
                let fileField = {
                    inFilelist: true,
                    directory: this.directory,
                    name: this.name,
                    filelist: this.filelist,
                    csrfToken: this.csrfToken,
                    labels: this.labels,
                    thumbnail: '',
                    extensions: this.extensions,
                };

                this.containerFiles.push(fileField);
            },
        },
    };
</script>
