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

class ForgotPassword extends React.Component{
    
    constructor(props) {

        super(props)

        //data auth/Register manages
        this.state = {

            form: {
                email: '',
            },

            alert: {
                show: false,
                type: '',
                text: '',
            },
            
        }//end of state

        this.cleartAlert = this.cleartAlert.bind(this)
        this.handleOnSubmitted = this.handleOnSubmitted.bind(this)

    }

    handleOnSubmitted(response) {

        this.setState({
            alert: {
                show: true,
                type: 'success',
                text: 'Please check your email to reset password.'
            }
        })


        setTimeout(() => {
            this.cleartAlert()
        }, 3000)

    }

    cleartAlert() {

        this.setState({
            alert: {
                show: false,
                type: '',
                text: ''
            }
        })

    }

    componentDidMount() {

        document.querySelector('form input').focus()

    }

    render() {

        return (

            <section style={{paddingTop: '3rem'}}>

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-4"></div>

                        <div className="mdc-layout-grid__cell--span-4">

                            <Alert show={this.state.alert.show} 
                            type={this.state.alert.type} 
                            text={this.state.alert.text} />

                        </div>

                        <div className="mdc-layout-grid__cell--span-4"></div>

                        <div className="mdc-layout-grid__cell--span-4"></div>

                        <div className="mdc-layout-grid__cell--span-4">

                            <FormikForm form={this.state.form} onSubmitted={this.handleOnSubmitted} />

                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

const ForgotPasswordForm = (props) => {

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

        <Form onSubmit={handleSubmit}>

            <div className="mdc-layout-grid__inner">

                <div className="mdc-layout-grid__cell--span-12">
                    
                    <div className="mdc-card">

                        <section className="mdc-card__primary mdc-theme--primary-bg">
                            <h1 className="mdc-card__title mdc-theme--primary-light">
                            Forgot Password
                            </h1>
                        </section>

                        <section className="mdc-card__media">

                            <div className="mdc-layout-grid__inner">

                                <div className="mdc-layout-grid__cell--span-12">
                                    
                                    <div className="mdc-text-field" style={{width: '100%'}}>
                                        <input  
                                        onChange={handleChange} 
                                        onBlur={handleBlur}
                                        value={values.email} 
                                        type="text" id="email" 
                                        name="email" className="mdc-text-field__input"
                                        placeholder="Email" />
                                        <div className="mdc-text-field__bottom-line"></div>
                                    </div>
                                    {touched.email && errors.email && 
                                    <p className="mdc-text-field-helper-text 
                                    mdc-text-field-helper-text--validation-msg form-error" 
                                    aria-live="polite">
                                    {errors.email}
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

        </Form>

    )

}


const FormikForm = withFormik({

    mapPropsToValues: (props) => ({ 
        email: props.form.email,
    }),

    validationSchema: Yup.object().shape({
        email: Yup.string()
            .email('This email is invalid.')
            .required('This field is required.'),
    }),

    handleSubmit: (values, { props, resetForm, setSubmitting }) => {
        
        setSubmitting(true)

        axios.post('/api/forgot/password', values).then((resp) => {

            //console.log(resp)

            props.onSubmitted(resp)

            if(resp.status && resp.status == 200){
                resetForm()
            }

        }).catch((err) => {

            props.onSubmitted(err)

        }).then(() => {

            setSubmitting(false)

        })

    },

})(ForgotPasswordForm)

export default withRouter(ForgotPassword)