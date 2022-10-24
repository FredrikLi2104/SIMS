<template>
    <div>
        <div class="col-md-12">
            <div class="mb-1">
                <label class="form-label" for="desc_en">{{collection?.messages?.desc}} {{collection?.messages?.inEnglish}} [{{collection?.messages?.optional}}]</label>
                <div id="desc_en_editor"></div>
                <input id="desc_en" name="desc_en" type="text" hidden />
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-1">
                <label class="form-label" for="desc_se">{{collection?.messages?.desc}} {{collection?.messages?.inSwedish}} [{{collection?.messages?.optional}}]</label>
                <div id="desc_se_editor"></div>
                <input id="desc_se" name="desc_se" type="text" hidden />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["locale", "sanctionid"],
    data() {
        return {
            collection: null,
        };
    },
    mounted() {
        var thisComponent = this;
        axios
            .get("/" + thisComponent.locale + "/axios/sanctions/admin/"+thisComponent.sanctionid, {})
            .then(function (response) {
                //console.log(response.data);
                thisComponent.collection = response.data;
                var descEnQuill = new Quill("#desc_en_editor", {
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
                });
                thisComponent.$nextTick(() => {
                    descEnQuill.setContents(JSON.parse(thisComponent.collection?.sanction?.desc_en));
                    descEnQuill.on("text-change", function () {
                        let enContent = descEnQuill.getContents();
                        document.getElementById("desc_en").value = JSON.stringify(enContent);
                    });
                });
                var descSeQuill = new Quill("#desc_se_editor", {
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
                });
                thisComponent.$nextTick(() => {
                    descSeQuill.setContents(JSON.parse(thisComponent.collection?.sanction?.desc_se));
                    descSeQuill.on("text-change", function () {
                        let seContent = descSeQuill.getContents();
                        document.getElementById("desc_se").value = JSON.stringify(seContent);
                    });
                });
            })
            .catch(function (error) {
                console.log(error);
                console.log(error.response);
            });
    },
};
</script>
