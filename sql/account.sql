USE [ASD]
GO

/****** Object:  Table [dbo].[account]    Script Date: 06/07/2013 19:53:21 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[account](
	[c_id] [char](20) NOT NULL,
	[c_sheadera] [varchar](255) NOT NULL,
	[c_sheaderb] [varchar](255) NOT NULL,
	[c_sheaderc] [varchar](255) NOT NULL,
	[c_headera] [varchar](255) NOT NULL,
	[c_headerb] [varchar](255) NOT NULL,
	[c_headerc] [varchar](255) NOT NULL,
	[d_cdate] [smalldatetime] NULL,
	[d_udate] [smalldatetime] NULL,
	[c_status] [char](1) NOT NULL,
	[m_body] [varchar](255) NULL,
	[acc_status] [varchar](50) NOT NULL,
	[salary] [smalldatetime] NOT NULL,
	[last_salary] [smalldatetime] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[account] ADD  CONSTRAINT [acc_status]  DEFAULT ('Normal') FOR [acc_status]
GO

ALTER TABLE [dbo].[account] ADD  CONSTRAINT [salary]  DEFAULT (1 / 1 / 2003) FOR [salary]
GO

ALTER TABLE [dbo].[account] ADD  CONSTRAINT [last_salary]  DEFAULT (1 / 1 / 2003) FOR [last_salary]
GO

