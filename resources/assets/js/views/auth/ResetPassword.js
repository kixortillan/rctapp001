import React from 'react'
import {
    withRouter,
    Redirect,
} from 'react-router-dom'
import QueryString from 'query-string'
import {MDCTextField} from '@material/textfield'
import {withFormik, Form} from 'formik'
import Yup from 'yup'

import Alert from '../../components/Alert'

class ResetPassword extends React.Component{
    
    constructor(props) {

        super(props)

        //data auth/Register manages
        this.state = {

            form: {
                old_password: '',
                new_password: '',
                new_password_confirmation: '',
            },

            alert: {
                show: false,
                text: '',
            },
            
        }//end of state

        this.cleartAlert = this.cleartAlert.bind(this)
        this.handleSubmitted = this.handleSubmitted.bind(this)

    }

    cleartAlert() {

        this.setState({alert: {
            show: false,
            text: ''
        }})

    }

    handleSubmitted(response) {
        console.log(response.response)
        if(response.status){

            this.setState({
                alert: {
                    show: true,
                    text: 'Password successfully changed.'
                }
            })

        } else if(response.response) {

            if(response.response.status == '422'){

                this.setState({
                    alert: {
                        show: true,
                        text: response.response.data.error
                    }
                })

            }

        } else {

        }

    }

    componentDidMount() {

        document.querySelector('form input').focus()

    }

    render() {

        return (

            <section style={{paddingTop: '3rem'}}>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-4" />

                        <div className="mdc-layout-grid__cell--span-4">
                            <Alert show={this.state.alert.show} 
                            type="error" text={this.state.alert.text} />
                        </div>

                    </div>

                </div>

                <FormikForm form={this.state.form} submitted={this.handleSubmitted} onLogin={this.props.onLogin} />

            </section>

        )

    }

}

const ResetPasswordForm = (props) => {

    const {
        values,
        touched,
        errors,
        dirty,
        isSubmitting,
        handleChange,
        setFieldValue,
        handleBlur,
        handleSubmit,
        handleReset,
    } = props

    return (

        <Form id="frm_forgot" onSubmit={handleSubmit}>

            <div className="mdc-layout-grid">

                <div className="mdc-layout-grid__inner">

                    <div className="mdc-layout-grid__cell--span-4" />

                    <div className="mdc-layout-grid__cell--span-4">
                        
                        <div className="mdc-card">

                            <section className="mdc-card__primary mdc-theme--primary-bg">
                                <h1 className="mdc-card__title mdc-theme--primary-light">
                                Change Password
                                </h1>
                            </section>

                            <section className="mdc-card__media">

                                <div className="mdc-layout-grid__inner">

                                    <div className="mdc-layout-grid__cell--span-12">
                                        
                                        <div className="mdc-text-field" style={{width: '100%'}}>
                                            <input  
                                            onChange={handleChange} 
                                            onBlur={handleBlur}
                                            value={values.old_password} 
                                            type="password" id="old_password" 
                                            name="old_password" className="mdc-text-field__input"
                                            placeholder="Current Password" />
                                            <div className="mdc-text-field__bottom-line"></div>
                                        </div>
                                        {touched.old_password && errors.old_password && 
                                        <p className="mdc-text-field-helper-text 
                                        mdc-text-field-helper-text--validation-msg form-error" 
                                        aria-live="polite">
                                        {errors.old_password}
                                        </p>}

                                    </div>

                                    <div className="mdc-layout-grid__cell--span-12">
                                        
                                        <div className="mdc-text-field" style={{width: '100%'}}>
                                            <input  
                                            onChange={handleChange} 
                                            onBlur={handleBlur}
                                            value={values.new_password} 
                                            type="password" id="new_password" 
                                            name="new_password" className="mdc-text-field__input"
                                            placeholder="New Password" />
                                            <div className="mdc-text-field__bottom-line"></div>
                                        </div>
                                        {touched.new_password && errors.new_password && 
                                        <p className="mdc-text-field-helper-text 
                                        mdc-text-field-helper-text--validation-msg form-error" 
                                        aria-live="polite">
                                        {errors.new_password}
                                        </p>}

                                    </div>

                                    <div className="mdc-layout-grid__cell--span-12">
                                        
                                        <div className="mdc-text-field" style={{width: '100%'}}>
                                            <input  
                                            onChange={handleChange} 
                                            onBlur={handleBlur}
                                            value={values.new_password_confirmation} 
                                            type="password" id="new_password_confirmation" 
                                            name="new_password_confirmation" className="mdc-text-field__input"
                                            placeholder="Re-type New Password" />
                                            <div className="mdc-text-field__bottom-line"></div>
                                        </div>
                                        {touched.new_password_confirmation && errors.new_password_confirmation && 
                                        <p className="mdc-text-field-helper-text 
                                        mdc-text-field-helper-text--validation-msg form-error" 
                                        aria-live="polite">
                                        {errors.new_password_confirmation}
                                        </p>}

                                    </div>

                                </div>

                            </section>

                            <section className="mdc-card__actions" dir="rtl">
                                <button
                                className="mdc-button mdc-card__action 
                                mdc-theme--primary-bg mdc-theme--primary-light">
                                Send
                                </button>
                            </section>

                        </div>

                    </div>

                </div>

            </div>

        </Form>

    )

}


const FormikForm = withFormik({

    mapPropsToValues: (props) => ({ 

        old_password: props.form.old_password,
        new_password: props.form.new_password,
        new_password_confirmation: props.form.new_password_confirmation,

    }),

    validationSchema: Yup.object().shape({

        old_password: Yup.string()
            .required('This field is required.')
            .min(8, 'Must not be less than 8 characters.')
            .max(150, 'Must not exceed 150 characters.'),

        new_password: Yup.string()
            .required('This field is required.')
            .min(8, 'Must not be less than 8 characters.')
            .max(150, 'Must not exceed 150 characters.'),

        new_password_confirmation: Yup.string()
            .required('This field is required.')
            .min(8, 'Must not be less than 8 characters.')
            .max(150, 'Must not exceed 150 characters.'),

    }),

    handleSubmit: (values, { props, setSubmitting }) => {
        
        const qs = QueryString.parse(window.location.search)

        axios.post('/api/change/password/' + qs.r , values).then((resp) => {

            console.log(resp)
            props.submitted(resp)
            props.onLogin(resp.data)


        }).catch((err) => {

            console.log(err)
            props.submitted(err)

        })    

        setSubmitting(false)

    },

})(ResetPasswordForm)

export default withRouter(ResetPassword)