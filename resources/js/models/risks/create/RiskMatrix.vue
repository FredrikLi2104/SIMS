<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.riskMatrix }}</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ collection?.messages?.riskMatrixDesc }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="25%">{{ collection?.messages?.probabilityVsConsequence }}</th>
                                <th width="15%" class="text-center">{{ collection?.messages?.negligible }}</th>
                                <th width="15%" class="text-center">{{ collection?.messages?.minor }}</th>
                                <th width="15%" class="text-center">{{ collection?.messages?.moderate }}</th>
                                <th width="15%" class="text-center">{{ collection?.messages?.significant }}</th>
                                <th width="15%" class="text-center">{{ collection?.messages?.severe }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="table-td-light">
                                    <span class="fw-bold">{{ collection?.messages?.veryLikely }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 5 && cons == 1 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 5 && cons == 2 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med-high fw-bold ${prob == 5 && cons == 3 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.mediumHigh }}</span>
                                </td>
                                <td :class="`text-center text-light bg-danger fw-bold ${prob == 5 && cons == 4 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.high }}</span>
                                </td>
                                <td :class="`text-center text-light bg-danger fw-bold ${prob == 5 && cons == 5 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.high }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-td-light">
                                    <span class="fw-bold">{{ collection?.messages?.likely }}</span>
                                </td>
                                <td :class="`text-center text-light bg-success fw-bold ${prob == 4 && cons == 1 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.low }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 4 && cons == 2 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 4 && cons == 3 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med-high fw-bold ${prob == 4 && cons == 4 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.mediumHigh }}</span>
                                </td>
                                <td :class="`text-center text-light bg-danger fw-bold ${prob == 4 && cons == 5 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.high }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-td-light">
                                    <span class="fw-bold">{{ collection?.messages?.possible }}</span>
                                </td>
                                <td :class="`text-center text-light bg-success fw-bold ${prob == 3 && cons == 1 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.low }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 3 && cons == 2 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 3 && cons == 3 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med-high fw-bold ${prob == 3 && cons == 4 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.mediumHigh }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med-high fw-bold ${prob == 3 && cons == 5 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.mediumHigh }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-td-light">
                                    <span class="fw-bold">{{ collection?.messages?.unlikely }}</span>
                                </td>
                                <td :class="`text-center text-light bg-success fw-bold ${prob == 2 && cons == 1 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.low }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 2 && cons == 2 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 2 && cons == 3 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 2 && cons == 4 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med-high fw-bold ${prob == 2 && cons == 5 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.mediumHigh }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-td-light">
                                    <span class="fw-bold">{{ collection?.messages?.veryUnlikely }}</span>
                                </td>
                                <td :class="`text-center text-light bg-success fw-bold ${prob == 1 && cons == 1 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.low }}</span>
                                </td>
                                <td :class="`text-center text-light bg-success fw-bold ${prob == 1 && cons == 2 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.low }}</span>
                                </td>
                                <td :class="`text-center text-light bg-med fw-bold ${prob == 1 && cons == 3 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.lowMed }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 1 && cons == 4 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                                <td :class="`text-center text-light bg-warning fw-bold ${prob == 1 && cons == 5 ? `` : `alpha-third`}`">
                                    <span>{{ collection?.messages?.medium }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="probability">{{collection?.messages?.probability}}</label>
                <select id="probability" class="form-select form-control" name="probability" v-model="prob">
                    <option v-for="i in 5" :key="i" :value="i">{{ i }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="consequence">{{collection?.messages?.consequence}}</label>
                <select id="consequence" class="form-select form-control" name="consequence" v-model="cons">
                    <option v-for="i in 5" :key="i" :value="i">{{ i }}</option>
                </select>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["locale"],
    data() {
        return {
            collection: null,
            prob: 1,
            cons: 1,
        };
    },
    mounted() {
        var thisComponent = this;
        axios
            .get(`/${thisComponent.locale}/axios/messages`, {})
            .then(function (response) {
                console.log(response.data);
                thisComponent.collection = response.data;
            })
            .catch(function (error) {
                console.log(error);
                console.log(error.response);
            });
    },
};
</script>
