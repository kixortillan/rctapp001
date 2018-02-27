import React from 'react'
import {
    withRouter,
    Redirect,
} from 'react-router-dom'
import QueryString from 'query-string'
import {MDCTextField} from '@material/textfield'
import {
    withFormik, 
    Form,
} from 'formik'
import Yup from 'yup'

import Alert from '../../components/Alert'
import MessageDialog from '../../components/MessageDialog'

class Register extends React.Component{
    
    constructor(props) {

        super(props)

        //data auth/Register manages
        this.state = {

            form: {
                username: '',
                email: '',
                password: '',
                password_confirmation: '',
            },

            alert: {
                show: false,
                text: '',
            },
            
        }//end of state

        this.cleartAlert = this.cleartAlert.bind(this)
        this.handleOnSubmitted = this.handleOnSubmitted.bind(this)

    }

    cleartAlert() {

        this.setState({alert: {
            show: false,
            text: ''
        }})

    }

    handleOnSubmitted(response) {

        this.setState({
            alert: {
                show: true,
                text: 'User has been successfully created.'
            },
            form: {
                username: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
        })


        setTimeout(() => {
            this.cleartAlert()
        }, 3000)

    }

    componentDidMount() {

        document.querySelector('form input').focus()
    }

    render() {

        return (

            <section>

                <Alert show={this.state.alert.show  } 
                type="success" text={this.state.alert.text} />

                <div className="mdc-layout-grid">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-4">
                            
                            <FormikForm form={this.state.form} onSubmitted={this.handleOnSubmitted} />

                        </div>

                    </div>

                </div>

            </section>

        )

    }

}

const RegisterForm = (props) => {

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

            <div className="mdc-card">

                <section className="mdc-card__primary mdc-theme--primary-bg">
                    <h1 className="mdc-card__title mdc-theme--primary-light">
                    Create New User
                    </h1>
                </section>

                <section className="mdc-card__media">

                    <div className="mdc-layout-grid__inner">

                        <div className="mdc-layout-grid__cell--span-12">
                            
                            <div className="mdc-text-field" style={{width: '100%'}}>
                                <input
                                onChange={handleChange} 
                                value={values.username} 
                                type="text" id="username" 
                                name="username" className="mdc-text-field__input" 
                                placeholder="Username" />
                                <div className="mdc-text-field__bottom-line"></div>
                            </div>
                            
                            {touched.username && errors.username && 
                            <p className="mdc-text-field-helper-text 
                            mdc-text-field-helper-text--validation-msg 
                            form-error" 
                            aria-hidden="true" 
                            aria-live="polite">
                            {errors.username}
                            </p>
                            }

                        </div>

                        <div className="mdc-layout-grid__cell--span-12">
                            
                            <div className="mdc-text-field" style={{width: '100%'}}>
                                <input
                                onChange={handleChange}  
                                value={values.email} 
                                type="email" id="email" 
                                name="email" className="mdc-text-field__input" 
                                placeholder="Email Address" />
                                <div className="mdc-text-field__bottom-line"></div>
                            </div>

                            {touched.email && errors.email && 
                            <p className="mdc-text-field-helper-text 
                            mdc-text-field-helper-text--validation-msg 
                            form-error" 
                            aria-hidden="true" 
                            aria-live="polite">
                            {errors.email}
                            </p>
                            }

                        </div>

                        <div className="mdc-layout-grid__cell--span-12">
                            
                            <div className="mdc-text-field" style={{width: '100%'}}>
                                <input
                                onChange={handleChange}  
                                value={values.password} 
                                type="password" id="password" 
                                name="password" className="mdc-text-field__input" 
                                placeholder="Password" />
                                <div className="mdc-text-field__bottom-line"></div>
                            </div>
                            
                            {touched.password && errors.password && 
                            <p className="mdc-text-field-helper-text 
                            mdc-text-field-helper-text--validation-msg 
                            form-error" 
                            aria-hidden="true" 
                            aria-live="polite">
                            {errors.password}
                            </p>
                            }

                        </div>

                        <div className="mdc-layout-grid__cell--span-12">
                            
                            <div className="mdc-text-field" style={{width: '100%'}}>
                                <input
                                onChange={handleChange} 
                                value={values.password_confirmation} 
                                type="password" id="password_confirmation" 
                                name="password_confirmation" 
                                className="mdc-text-field__input" 
                                placeholder="Re-type Password" />
                                <div className="mdc-text-field__bottom-line"></div>
                            </div>
                            
                            {touched.password_confirmation && errors.password_confirmation && 
                            <p className="mdc-text-field-helper-text 
                            mdc-text-field-helper-text--validation-msg 
                            form-error" 
                            aria-hidden="true" 
                            aria-live="polite">
                            {errors.password_confirmation}
                            </p>
                            }

                        </div>

                    </div>

                </section>

                <section className="mdc-card__actions" dir="rtl">
                    <button
                    className="mdc-button mdc-card__action 
                    mdc-button--dense mdc-theme--primary-bg 
                    mdc-theme--primary-light">
                    Create
                    </button>
                </section>

            </div>

        </Form>

    )

}

const FormikForm = withFormik({

    mapPropsToValues: (props) => ({ 
        
        username: props.form.username,
        email: props.form.email,
        password: props.form.password,
        password_confirmation: props.form.password_confirmation,

    }),

    validationSchema: Yup.object().shape({

        username: Yup.string()
            .required('This field is required.')
            .matches(/^[a-zA-Z]+[a-zA-Z0-9._]*$/, 
                'Must only contain letters, numbers, period(.) and underscore(_).')
            .max(150, 'Must not exceed 150 characters.'),
        email: Yup.string()
            .email('This email is invalid.')
            .required('This field is required.'),
        password: Yup.string()
            .required('This field is required.')
            .min(8, '')
            .max(150, ''),
        password_confirmation: Yup.string()
            .required('This field is required.')
            .oneOf([Yup.ref('password'), null], 'Passwords don\'t match.')
            .min(8, '')
            .max(150, ''),

    }),

    handleSubmit: (values, { props, resetForm, setSubmitting }) => {
        
        axios.post('/api/users/register', values).then((resp) => {

            //console.log(resp)
            props.onSubmitted(resp)            

            if(resp.status && resp.status == 200){
                resetForm()
            }

        }).catch((err) => {

            console.log(err)

        })

        setSubmitting(false)

    },

})(RegisterForm)

export default withRouter(Register)