pipeline {
    agent any

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/UbaidSaroya/portfolio.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    docker.build('ubaid-portfolio')
                }
            }
        }

        stage('Test Docker Container') {
            steps {
                script {
                    // Note: Port changed to 8081 to avoid Jenkins conflict
                    docker.image('ubaid-portfolio').withRun('-p 8081:80') { c ->
                        sh 'sleep 5'
                        sh 'curl -f http://localhost:8081'
                    }
                }
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: ['ec2-ssh']) {
                    sh '''
                    ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 '
                        docker stop portfolio || true
                        docker rm portfolio || true
                        docker rmi ubaid-portfolio || true
                    '

                    docker save ubaid-portfolio | bzip2 | \
                    ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 'bunzip2 | docker load'

                    ssh -o StrictHostKeyChecking=no ec2-user@15.207.113.34 '
                        docker run -d -p 80:80 --name portfolio ubaid-portfolio
                    '
                    '''
                }
            }
        }
    }
}
