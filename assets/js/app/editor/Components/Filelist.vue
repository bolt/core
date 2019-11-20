<template>
    <div>
        <div
                v-for="(child, index) in containerFiles"
                :key="child.id"
                class="form-fieldsgroup"
        >
            <editor-file
                    :filename="child.filename"
                    :thumbnail="child.thumbnail"
                    :title="child.title"
                    :attributes-link="attributesLink"
                    :media="child.media"
                    :directory="directory"
                    :filelist="filelist"
                    :csrf-token="csrfToken"
                    :labels="labels"
                    :in-filelist="true"
                    :name="fieldName(index)"
                    :extensions="extensions"
                    :is-first-in-filelist="isFirstInFilelist(index)"
                    :is-last-in-filelist="isLastInFilelist(index)"
                    @remove="onRemoveFile"
                    @moveFileUp="onMoveFileUp"
                    @moveFileDown="onMoveFileDown"
            ></editor-file>
        </div>

        <button class="btn btn-secondary" type="button" @click="addFile">
            {{ labels.add_new_file }}
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
            let counter = 0;
            let containerFiles = this.files;
            containerFiles.forEach(function(file, index, theContainerFilesArray){
                theContainerFilesArray[index].id = index;
                counter++;
            });

            return {
                counter,
                containerFiles,
            };
        },
        methods: {
            isFirstInFilelist(index) {
                return index === 0;
            },
            isLastInFilelist(index) {
                return index === this.containerFiles.length - 1;
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
            onRemoveFile(elem) {
                let fieldNumber = this.getFieldNumberFromElement(elem);
                this.containerFiles.splice(fieldNumber, 1);
                if (this.containerFiles.length === 0) {
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
                    id: this.counter,
                };

                this.counter++;
                this.containerFiles.push(fileField);
            },
        },
    };
</script>
