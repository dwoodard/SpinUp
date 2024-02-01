 # Spin UP

## A Project Generator for Laravel, Breeze (vue) and Laradock
 


 

```mermaid
flowchart TD

    CheckIfProjectExists[handleIfExsistingProject] -->|yes| InstallLaravel[installLaravel]
CheckIfProjectExists -->|no| CreateNewProject[createProject]
InstallLaravel --> InstallBreeze[installBreeze]
InstallBreeze --> AskToInstallLaradock[installLaradock]
AskToInstallLaradock -->|yes| InstallLaradock
CreateNewProject --> InstallLaravel
CreateNewProject --> InstallLaradock
    
```

 
