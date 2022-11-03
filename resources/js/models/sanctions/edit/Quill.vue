<template>
    <div>
        <div class="col-md-12">
            <div class="mb-1">
                <label class="form-label" for="desc_en">{{ labelEn }}</label>
                <div id="desc_en_editor"></div>
                <input id="desc_en" name="desc_en" type="text" :value="descEn" hidden/>
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-1">
                <label class="form-label" for="desc_se">{{ labelSe }}</label>
                <div id="desc_se_editor"></div>
                <input id="desc_se" name="desc_se" type="text" :value="descSe" hidden/>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["locale", "labelEn", "labelSe", "oldDescEn", "oldDescSe"],
    data() {
        return {
            collection: null,
            descEn: this.oldDescEn,
            descSe: this.oldDescSe,
        };
    },
    methods: {
        quillOptions() {
            return {
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [
                            {
                                font: [],
                            },
                            {
                                size: [],
                            },
                        ],
                        ["bold", "italic", "underline", "strike"],
                        [
                            {
                                color: [],
                            },
                            {
                                background: [],
                            },
                        ],
                        [
                            {
                                script: "super",
                            },
                            {
                                script: "sub",
                            },
                        ],
                        [
                            {
                                header: "1",
                            },
                            {
                                header: "2",
                            },
                            "blockquote",
                            "code-block",
                        ],
                        [
                            {
                                list: "ordered",
                            },
                            {
                                list: "bullet",
                            },
                            {
                                indent: "-1",
                            },
                            {
                                indent: "+1",
                            },
                        ],
                        [
                            "direction",
                            {
                                align: [],
                            },
                        ],
                        ["link", "image", "video", "formula"],
                        ["clean"],
                    ],
                },
                theme: "snow",
            };
        }
    },
    mounted() {
        const thisComponent = this;
        let descEnQuill = new Quill("#desc_en_editor", thisComponent.quillOptions());
        let descSeQuill = new Quill("#desc_se_editor", thisComponent.quillOptions());

        descEnQuill.setContents(JSON.parse(thisComponent.descEn));
        descEnQuill.on("text-change", function () {
            let enContent = descEnQuill.getContents();
            document.getElementById("desc_en").value = JSON.stringify(enContent);
        });

        descSeQuill.setContents(JSON.parse(thisComponent.descSe));
        descSeQuill.on("text-change", function () {
            let seContent = descSeQuill.getContents();
            document.getElementById("desc_se").value = JSON.stringify(seContent);
        });
    },
};
</script>
