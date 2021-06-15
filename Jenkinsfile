pipeline {
    agent any

      environment {

          OCP_CREDS = credentials ('jenkins-crc-iam')

      }

    stages {
        stage('Build Image') {
            steps {
                sh "docker build --no-cache --target symfony_php --build-arg SYMFONY_VERSION=- --build-arg STABILITY=-stable -t iam-php ."
                sh "docker tag iam-php quay.io/fauzislami/iam-php:latest"
                sh "docker tag iam-php quay.io/fauzislami/iam-php:v1-dev.${env.BUILD_ID}"
            }
        }

        stage('Push Image to Registry') {
            steps {
                sh "docker push quay.io/fauzislami/iam-php:v1-dev.${env.BUILD_ID}"
            }
        }

        stage('Deploy to Dev') {
            steps {

                script {
                           echo "deploy to dev"
                           sh 'oc login --token=$OCP_CREDS --server=https://api.crc.testing:6443'
                           sh "sed -i s/UPDATE/v1-dev.${env.BUILD_ID}/g deployments/iam-deployment-dev.yaml"
                           sh "oc apply -f deployments/iam-deployment-dev.yaml"
                }

            }
        }

        stage('Approval deployment to OCP-Sim') {
            steps {
                script {
                    input 'Promote deployment to OCP-Sim ?'
                }
            }
        }

        stage('Tag Image for Sim') {
            steps {
                sh "docker tag iam-php quay.io/fauzislami/iam-php:v1-sim.${env.BUILD_ID}"
                sh "docker push quay.io/fauzislami/iam-php:v1-sim.${env.BUILD_ID}"
            }
        }

        stage('Deploy to Sim') {
            steps {

                script {
                           echo "deploy to sim"
                           sh 'oc login --token=$OCP_CREDS --server=https://api.crc.testing:6443'
                           sh "sed -i s/UPDATE/v1-sim.${env.BUILD_ID}/g deployments/iam-deployment-sim.yaml"
                           sh "oc apply -f deployments/iam-deployment-sim.yaml"
                }

            }
        }

        stage('Approval deployment to OCP-Prod') {
            steps {
                script {
                    input 'Promote deployment to OCP-Prod ?'
                }
            }
        }

        stage('Tag Image for Prod') {
            steps {
                sh "docker tag iam-php quay.io/fauzislami/iam-php:v1-prod.${env.BUILD_ID}"
                sh "docker push quay.io/fauzislami/iam-php:v1-prod.${env.BUILD_ID}"
            }
        }

        stage('Deploy to Prod') {
            steps {

                script {
                           echo "deploy to prod"
                           sh 'oc login --token=$OCP_CREDS --server=https://api.crc.testing:6443'
                           sh "sed -i s/UPDATE/v1-prod.${env.BUILD_ID}/g deployments/iam-deployment-prod.yaml"
                           sh "oc apply -f deployments/iam-deployment-prod.yaml"
                }

            }
        }


    }
}
