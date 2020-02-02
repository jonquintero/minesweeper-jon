<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">CREATE GAME</div>

                    <div class="card-body">
                        <form @submit.prevent="newGame">


                            <div class="form-group row">
                                <label  class="col-md-4 col-form-label text-md-right">Rows</label>

                                <div class="col-md-6">
                                    <input type="number" v-model="form.rows" id="rows" placeholder="Number row">
                                    <small class="form-control" v-if="errors.rows" v-text="errors.rows[0]"></small>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Columns</label>

                                <div class="col-md-6">
                                    <input type="number" v-model="form.columns" id="columns" placeholder="Number Columns">
                                    <small class="form-control" v-if="errors.columns" v-text="errors.columns[0]"></small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">Mines</label>
                                <div class="col-md-6">
                                    <input type="number" v-model="form.mines" id="mines" placeholder="Number Mines">
                                    <small class="form-control" v-if="errors.mines" v-text="errors.mines[0]"></small>
                                </div>
                            </div>


                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        New Game
                                    </button>


                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                num_rows: null,
                num_columns: null,
                num_mines: null,
                status: '',
                success: false,
                message: '',
                form : {},
                errors: {},
            }
        },
        created() {
            let self = this;

            this.status = 'playing';


            this.initForm();

        },

        methods: {
            newGame() {
                this.status = '';
                this.message = '';
                this.success = false;

                let uri = '/api/game';
                this.axios.post(uri, this.form).then((response) => {


                    if (response.data.success) {

                        this.$router.push({name: 'game', params:{id:response.data.id}});
                    } else {
                        // this.message.error(response.data.message)
                    }
                })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        }
                    });
              
            },
            initForm() {
                this.errors = {};
                this.form = {
                    mines: null,
                    columns: null,
                    rows: null,
                }
            },



        }
    }
</script>
